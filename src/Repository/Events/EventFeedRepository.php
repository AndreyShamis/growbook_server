<?php

namespace App\Repository\Events;

use App\Entity\Events\EventFeed;
use App\Entity\Events\EventHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventFeed|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventFeed|null findOneBy(array $criteria, array $orderBy = null)

 * @method EventFeed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventFeedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventFeed::class);
    }

    /**
     * @return EventFeed[]
     */
    public function findAll(): array
    {
       return parent::findBy(['type' => EventFeed::class]);
    }
}
