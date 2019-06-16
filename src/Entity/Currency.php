<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Currency
 *
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="currency")
 */
class Currency implements \JsonSerializable
{
    
    use CreateUpdateTrait;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=10, nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="code", type="string", length=10, nullable=false)
     */
    protected $code;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\CurrencyPair", mappedBy="source")
     */
    protected $sourceForPairs;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\CurrencyPair", mappedBy="target")
     */
    protected $targetForPairs;
    
    public function __construct()
    {
        $this->sourceForPairs = new ArrayCollection();
        $this->targetForPairs = new ArrayCollection();
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
    
    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function jsonSerialize()
    {
        return [
            $this->getId(),
            $this->getCode(),
        ];
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSourceForPairs(): ArrayCollection
    {
        return $this->sourceForPairs;
    }
    
    /**
     * @param ArrayCollection $sourceForPairs
     *
     * @return $this
     */
    public function setSourceForPairs(ArrayCollection $sourceForPairs): self
    {
        $this->sourceForPairs = $sourceForPairs;
        
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getTargetForPairs(): ArrayCollection
    {
        return $this->targetForPairs;
    }
    
    /**
     * @param ArrayCollection $targetForPairs
     *
     * @return $this
     */
    public function setTargetForPairs(ArrayCollection $targetForPairs): self
    {
        $this->targetForPairs = $targetForPairs;
        
        return $this;
    }
}