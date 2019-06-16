<?php

namespace App\Repository;

use App\Entity\CurrencyPair;
use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ExchangeRateRepository
 *
 * @package App\Repository
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }
    
    /**
     * @param CurrencyPair $currencyPair
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastRateByCurrencyPair(CurrencyPair $currencyPair)
    {
        return $this->createQueryBuilder('er')
                    ->andWhere('er.currencyPair = :currencyPairId')
                    ->setParameter(':currencyPairId', $currencyPair)
                    ->addOrderBy('er.date', Criteria::DESC)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();
    }
}