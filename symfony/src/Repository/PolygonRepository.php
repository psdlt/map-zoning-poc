<?php

namespace App\Repository;

use App\Entity\Polygon;
use App\Model\Point;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Polygon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Polygon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Polygon[]    findAll()
 * @method Polygon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PolygonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Polygon::class);
    }

    /**
     * @param Point $point
     * @return Polygon[]
     */
    public function findMatchingZones(Point $point)
    {
        $qb = $this
            ->createQueryBuilder('p')
            //->select('p.zone')
            ->where('POLYGON_HAS_POINT(p.polygon, :lat, :lon) = true')
            ->setParameters([
                'lat' => $point->lat,
                'lon' => $point->lng,
            ])
        ;

        return $qb->getQuery()->getResult();
    }
}
