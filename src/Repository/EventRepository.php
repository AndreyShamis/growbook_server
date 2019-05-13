<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;
use App\Repository\Events\EventTemperatureRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findAllTypes()
    {
        $ret = array();
        $em = $this->getEntityManager();

        $tmps = $em->getRepository(EventTemperature::class);
        $hums = $em->getRepository(EventHumidity::class);
        $res1 = $tmps->findAll();
        $res2 = $hums->findAll();
        $ret = array_merge($res1, $res2);
        return $ret;
    }
//
//    public function find($id, $lockMode = null, $lockVersion = null)
//    {
//        $em = $this->getEntityManager();
//
//        $logRepo = $em->getRepository(EventTemperature::class);
//        $res = $logRepo->find($id);
////        $res = $this->createQueryBuilder('e')
////            ->setMaxResults(10);
////
////
////        $res = $res->getQuery()->getResult();
//        return $res;
//    }

    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
