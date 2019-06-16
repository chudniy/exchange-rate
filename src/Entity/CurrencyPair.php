<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CurrencyPair
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="currency_pair")
 */
class CurrencyPair implements \JsonSerializable
{
    use CreateUpdateTrait;
    
    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="sourceForPairs")
     */
    protected $source;
    
    /**
     * @var Currency
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="targetForPairs")
     */
    protected $target;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\ExchangeRate", mappedBy="currencyPair")
     */
    protected $exchangeRates;
    
    public function __construct()
    {
        $this->exchangeRates = new ArrayCollection();
    }
    
    public function jsonSerialize()
    {
        return [
            'id'   => $this->getId(),
            'code' => "{$this->source->getCode()}{$this->target->getCode()}",
            'name' => "{$this->source->getCode()}/{$this->target->getCode()}",
        ];
    }
    
    /**
     * @return Currency
     */
    public function getSource(): Currency
    {
        return $this->source;
    }
    
    /**
     * @param Currency $source
     *
     * @return $this
     */
    public function setSource(Currency $source): self
    {
        $this->source = $source;
        
        return $this;
    }
    
    /**
     * @return Currency
     */
    public function getTarget(): Currency
    {
        return $this->target;
    }
    
    /**
     * @param Currency $target
     *
     * @return $this
     */
    public function setTarget(Currency $target): self
    {
        $this->target = $target;
        
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getExchangeRates(): ArrayCollection
    {
        return $this->exchangeRates;
    }
    
    /**
     * @param ArrayCollection $exchangeRates
     *
     * @return $this
     */
    public function setExchangeRates(ArrayCollection $exchangeRates): self
    {
        $this->exchangeRates = $exchangeRates;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getSource()->getCode() . $this->getTarget()->getCode();
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getSource()->getCode() . '/' . $this->getTarget()->getCode();
    }
}