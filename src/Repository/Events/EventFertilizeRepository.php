<?php

namespace App\Repository\Events;

use App\Entity\Events\EventFertilize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventFertilize|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventFertilize|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventFertilize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventFertilizeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventFertilize::class);
    }

    /**
     * @return EventFertilize[]
     */
    public function findAll(): array
    {
        return parent::findBy(['type' => EventFertilize::class]);
    }
}
