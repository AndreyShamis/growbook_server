<?php

namespace App\Repository;

use App\Entity\NodeCommand;
use App\Entity\Plant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NodeCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method NodeCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method NodeCommand[]    findAll()
 * @method NodeCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NodeCommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NodeCommand::class);
    }

    /**
     * @param string $key
     * @param Plant $pant
     * @return NodeCommand
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findOrCreate(string $key, Plant $pant): NodeCommand
    {
        $entity = $this->findOneBy([
            'cmd_key' => $key,
            'plant' => $pant
        ]);
        if ($entity === null) {
            $entity = new NodeCommand();
            $entity->setPlant($pant);
            $entity->setCmdKey($key);
            $this->_em->persist($entity);
            $this->_em->flush();
        }
        return $entity;
    }
    // /**
    //  * @return NodeCommand[] Returns an array of NodeCommand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NodeCommand
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
