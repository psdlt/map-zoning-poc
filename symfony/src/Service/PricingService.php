<?php

namespace App\Service;

use App\Entity\Pricing;
use App\Entity\Zone;
use App\Repository\PricingRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class PricingService
{
    private $repo;
    private $zones;
    private $em;

    public function __construct(
        PricingRepository $repo,
        ZoneRepository $zones,
        EntityManagerInterface $entityManager
    ) {
        $this->repo = $repo;
        $this->zones = $zones;
        $this->em = $entityManager;
    }

    public function setPricing(string $from, string $to, string $price)
    {
        $fromZone = $this->zones->find($from);
        $toZone = $this->zones->find($to);

        // should be handled by form validator at POST, but...
        if (!$fromZone || !$toZone) {
            throw new InvalidArgumentException();
        }

        // do we already have pricing for this pair?
        $pricing = $this->repo->findOneBy([
            'fromZone' => $fromZone,
            'toZone' => $toZone,
        ]);

        if (!$pricing) {
            // create new
            $pricing = new Pricing();
            $pricing->setFromZone($fromZone);
            $pricing->setToZone($toZone);
        }

        // update price
        // in a production app you should worry about versioning, etc, but this is PoC
        $pricing->setPrice((float)$price);

        // persist
        $this->em->persist($pricing);
        $this->em->flush();
    }

    public function getPricingsFrom(Zone $zone)
    {
        $pricings = $this->repo->findBy([
            'fromZone' => $zone,
        ]);

        return array_map(function (Pricing $pricing) {
            // should do this in a transformer...
            return new \App\Model\Pricing(
                $pricing->getFromZone()->getUuid(),
                $pricing->getToZone()->getUuid(),
                $pricing->getPrice()
            );
        }, $pricings);
    }
}
