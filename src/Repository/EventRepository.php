<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Plant;
use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Model\PlantInterface;

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

    public function findByIdAndType($id, $type)
    {
        $em = $this->getEntityManager();
        $logRepo = $em->getRepository($type);
        return $logRepo->find($id);
    }

    /**
     * @param $type
     * @param Plant $plant
     * @param Sensor $sensor
     * @return mixed
     */
    public function findLast($type, Plant $plant, Sensor $sensor): ?Event
    {
        $ret = null;
        $sec = $sensor->getWriteForceEveryXseconds();
        try {
            $ret = $this->createQueryBuilder('e')
                ->where('e.createdAt >= :timeVal')
                ->andWhere('e.plant = :plant')
                ->andWhere('e.sensor = :sensor')
                ->andWhere('e.type = :type')
                ->setParameters([
                    'plant' => $plant,
                    'sensor' => $sensor,
                    'timeVal'=> new \DateTime('-'. $sec . ' seconds'),
                    'type' => $type
                ])
                ->orderBy('e.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()
            ;
            if (count($ret)) {
                $ret = $ret[0];
            } else {
                $ret = null;
            }
        } catch (\Throwable $ex) {
        }

        return $ret;
        // new \DateTime('now')
    }

    /**
     * @param PlantInterface $plant
     * @param int $hours
     * @param null $limit
     * @return Event[] Returns an array of Event objects
     * @throws \Exception
     */
    public function findAllByPlant(PlantInterface $plant, int $hours=24, $limit=null): array
    {
        $q = $this->createQueryBuilder('e')
            ->andWhere('e.plant = :plant')
            ->andWhere('e.createdAt >= :givenDate')
            ->setParameter('plant', $plant->getId())
            ->setParameter('givenDate', new \DateTime('-' . $hours . ' hours'))
            ->orderBy('e.id', 'DESC')
            ->addOrderBy('e.sensor', 'ASC')
//            ->orderBy('e.sensor', 'ASC')
//            ->addOrderBy('e.id', 'DESC')
            ;
        if ($limit !== null && $limit > 0) {
            $q->setMaxResults($limit);
        }
        return $q->getQuery()->getResult();
    }

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
