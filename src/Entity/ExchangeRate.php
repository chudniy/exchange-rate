<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExchangeRate
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRateRepository")
 * @ORM\Table(name="exchange_rate")
 */
class ExchangeRate implements \JsonSerializable
{
    
    use CreateUpdateTrait;
    
    /**
     * @var float|int
     *
     * @ORM\Column(name="rate", type="float", nullable=false)
     * @Assert\NotNull()
     */
    private $rate;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotNull()
     */
    protected $date;
    
    /**
     * @var CurrencyPair
     * @ORM\ManyToOne(targetEntity="App\Entity\CurrencyPair", inversedBy="exchangeRates")
     */
    protected $currencyPair;
    
    public function jsonSerialize()
    {
        return [
            $this->getDate()->getTimestamp() * 1000,
            $this->getRate(),
        ];
    }
    
    /**
     * @return float|int
     */
    public function getRate()
    {
        return $this->rate;
    }
    
    /**
     * @param float|int $rate
     *
     * @return $this
     */
    public function setRate($rate): self
    {
        $this->rate = $rate;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }
    
    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        
        return $this;
    }
    
    /**
     * @return CurrencyPair
     */
    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }
    
    /**
     * @param CurrencyPair $currencyPair
     *
     * @return $this
     */
    public function setCurrencyPair(CurrencyPair $currencyPair): self
    {
        $this->currencyPair = $currencyPair;
        
        return $this;
    }
}