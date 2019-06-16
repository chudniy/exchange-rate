<?php

namespace App\Services;

use App\Entity\CurrencyPair;
use App\Entity\ExchangeRate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExchangeService
{
    const API_BASE_URL = 'https://apiv2.bitcoinaverage.com/indices/global/history/';
    const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36';
    
    /**
     * @var int
     */
    protected $attempts = 0;
    
    /**
     * @var int
     */
    protected $maxAttempts = 3;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Client
     */
    protected $client;
    
    /**
     * @var RegistryInterface
     */
    protected $doctrine;
    
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    
    public function __construct(RegistryInterface $doctrine, SerializerInterface $serializer)
    {
        $this->client = $this->getNewHttpClient();
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
    }
    
    /**
     * @return Client
     */
    protected function getNewHttpClient()
    {
        $config = [
            'base_uri' => static::API_BASE_URL,
            'headers' => [
                'User-Agent' => static::USER_AGENT,
                'X-Testing' => 'testing',
            ],
            'cookies' => true,
        ];
        
        return new Client($config);
    }
    
    /**
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @throws GuzzleException|\Exception
     * @return string
     */
    protected function sendRequest($method, $endpoint = '', $params = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $params);
            $responseBody = $response->getBody()->getContents();
            
            return $responseBody;
        } catch (\Exception $error) {
            if (503 == $error->getCode()) {
                if ($this->attempts < $this->maxAttempts) {
                    sleep(30);
                    ++$this->attempts;
                    
                    return $this->sendRequest($method, $endpoint, $params);
                }
            }
            
            $this->logger->error($error->getMessage(), $error->getTrace());
            throw $error;
        }
    }
    
    /**
     * @param CurrencyPair|null $currencyPair
     *
     * @return array
     * @throws \Exception
     * @throws GuzzleException
     */
    public function getNewRates(CurrencyPair $currencyPair = null)
    {
        $result = [];
        if ($currencyPair) {
            $currencyPairs[] = $currencyPair;
        } else {
            $currencyPairs = $this->doctrine->getRepository(CurrencyPair::class)->findAll();
        }
    
        foreach ($currencyPairs as $currencyPair) {
            $rateData = $this->ratesRequest($currencyPair);
            $rateData = $this->filterNewRates($rateData, $currencyPair);
    
            foreach ($rateData as $rateItem) {
                $result[] = $this->normalize($rateItem, $currencyPair);
            }
        }
        
        return $result;
    }
    
    /**
     * @param CurrencyPair $currencyPair
     *
     * @return array
     * @throws GuzzleException
     */
    private function ratesRequest($currencyPair)
    {
        $jsonResponse = $this->sendRequest('GET', $this::API_BASE_URL . $currencyPair->getCode(), [
            'query' => [
                'period' => 'monthly',
                'format' => 'json'
            ]
        ]);
        $result = json_decode($jsonResponse, true);
        
        return $result;
    }
    
    /**
     * @param $rateData
     * @param CurrencyPair $currencyPair
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function filterNewRates($rateData, CurrencyPair $currencyPair)
    {
        $lastRate = $this->doctrine->getRepository(ExchangeRate::class)->getLastRateByCurrencyPair($currencyPair);
        $lastRateDate = $lastRate ? $lastRate[0]->getDate() : null;
    
        if ($lastRateDate) {
            $result = array_filter($rateData, function ($rateItem) use ($lastRateDate) {
                $date = new \DateTime($rateItem['time']);
                return $date > $lastRateDate;
            });
    
            $rateData = $result;
        }
        
        return $rateData;
    }
    
    private function normalize($data, CurrencyPair $currencyPair)
    {
        $object = new ExchangeRate();
        $date = new \DateTime($data['time']);
        
        return $object
            ->setCurrencyPair($currencyPair)
            ->setRate($data['average'])
            ->setDate($date)
            ;
    }
}