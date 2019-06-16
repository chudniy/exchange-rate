<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\CurrencyPair;
use App\Services\ExchangeService;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateExchangeRateCommand extends Command
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;
    
    /**
     * @var ExchangeService
     */
    protected $exchangeService;
    
    public function __construct(RegistryInterface $doctrine, ExchangeService $exchangeService, ?string $name = null)
    {
        parent::__construct($name);
        $this->doctrine = $doctrine;
        $this->exchangeService = $exchangeService;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update-exchange-rate')
            ->setDescription('Update exchange rates for currency pairs')
            ->addArgument(
                'pair',
                InputArgument::OPTIONAL,
                "Currency pair in format 'BTC/USD'"
            );
    }
    
    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->doctrine->getEntityManager();
        $name = $input->getArgument('pair');
        $currencyPair = null;
        
        if ($name) {
            $currencyCodes = explode('/', $name);
            if ($currencyCodes && $currencyCodes[0] && $currencyCodes[1]) {
                if ( !$sourceCurrency = $em->getRepository(Currency::class)->findOneBy(['code' => $currencyCodes[0]])) {
                    throw new \Exception(sprintf('Wrong source currency code: %s', $currencyCodes[0]));
                }
                if ( !$targetCurrency = $em->getRepository(Currency::class)->findOneBy(['code' => $currencyCodes[1]])) {
                    throw new \Exception(sprintf('Wrong target currency code: %s', $currencyCodes[1]));
                }
                
                $currencyPair = $em->getRepository(CurrencyPair::class)->findOneBy(['source' => $sourceCurrency, 'target' => $targetCurrency]);
            } else {
                throw new \Exception(sprintf('Wrong currency pair format: %s', $name));
            }
        }
    
        $output->writeln('Updating in progress...');
        
        $newRates = $this->exchangeService->getNewRates($currencyPair);
    
        if ($newRates) {
            foreach ($newRates as $rate) {
                $em->persist($rate);
            }
            $em->flush();
            $output->writeln('Rates was updated');
        } else {
            $output->writeln('No updates for exchange rates');
        }
    }
}