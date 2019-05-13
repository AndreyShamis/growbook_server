<?php

namespace App\Repository\Events;

use App\Entity\Events\EventHumidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventHumidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventHumidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventHumidity[]    findAll()
 * @method EventHumidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventHumidityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventHumidity::class);
    }
}
