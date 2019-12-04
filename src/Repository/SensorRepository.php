<?php

namespace App\Repository;

use App\Entity\Sensor;
use App\Model\TypeEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensor[]    findAll()
 * @method Sensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    /**
     * @param string $uniqId
     * @return Sensor|null
     */
    public function findByUniqId(string $uniqId): ?Sensor
    {
        return $this->findOneBy(array('uniqId' => $uniqId));
    }

    /**
     * @param array $criteria
     * @param bool $supportEvents
     * @return Sensor
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findOrCreateByUniqId(array $criteria, bool $supportEvents=true): Sensor
    {
        $entity = $this->findByUniqId($criteria['uniqId']);

        if ($entity === null) {
            $entity = new Sensor();
            $entity->setUniqId($criteria['uniqId']);
            $entity->setDiffThreshold(10);
            $entity->setSupportEvents($supportEvents);
            //if (array_key_exists('eventType', $criteria)) {
            $entity->setEventType($criteria['eventType']);
            if ($entity->getEventType() === TypeEvent::Humidity) {
                $entity->setDiffThreshold(2);
            } else if ($entity->getEventType() === TypeEvent::Temperature) {
                $entity->setDiffThreshold(0.5);
            } else if ($entity->getEventType() === TypeEvent::SoilHydrometer) {
                $entity->setDiffThreshold(3);
            }

            if (array_key_exists('plant', $criteria) && $criteria['plant'] !== null) {
                $entity->setPlant($criteria['plant']);
            }
            if (array_key_exists('name', $criteria)) {
                $entity->setName($criteria['name']);
            }

            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
        return $entity;
    }

    // /**
    //  * @return Sensor[] Returns an array of Sensor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sensor
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
