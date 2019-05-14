<?php

namespace App\Repository;

use App\Entity\Plant;
use App\Utils\RandomName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Plant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plant[]    findAll()
 * @method Plant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Plant::class);
    }

    public function findOrCreate($criteria)
    {
        if (array_key_exists('id', $criteria)) {
            $entity = $this->find($criteria['id']);
        } else if (array_key_exists('uniqId', $criteria)) {
            $entity = $this->findOneBy(['uniqId' => $criteria['uniqId']]);
        } else {
            $entity = $this->findOneBy($criteria);
        }

        if ($entity === null) {
            $entity = new Plant();
            if (array_key_exists('name', $criteria)) {
                $entity->setName($criteria['name']);
            } else {
                $entity->setName(RandomName::getRandomTerm());
            }
            if (array_key_exists('uniqId', $criteria)) {
                $entity->setUniqId($criteria['uniqId']);
            } else {
                $entity->setUniqId($entity->getName());
            }
        }
        return $entity;
    }
    // /**
    //  * @return Plant[] Returns an array of Plant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Plant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
