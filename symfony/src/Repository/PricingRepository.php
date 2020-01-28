<?php

namespace App\Repository;

use App\Entity\Pricing;
use App\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Pricing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pricing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pricing[]    findAll()
 * @method Pricing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pricing::class);
    }

    /**
     * @param Zone $zone
     * @return Pricing[]
     */
    public function getRelatedPricing(Zone $zone)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->where('p.fromZone = :zone')
            ->orWhere('p.toZone = :zone')
            ->setParameter('zone', $zone)
        ;

        return $qb->getQuery()->getResult();
    }
}
