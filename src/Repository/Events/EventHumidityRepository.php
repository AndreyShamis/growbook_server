<?php

namespace App\Repository\Events;

use App\Entity\Events\EventHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventHumidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventHumidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventHumidity[]    findAll()
 * @method EventHumidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventHumidityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventHumidity::class);
    }
}
