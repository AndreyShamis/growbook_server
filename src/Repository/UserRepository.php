<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\RandomString;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $criteria
     * @return User
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(array $criteria): User
    {
        $criteria['username'] = strtolower($criteria['username']);
        $criteria['email'] = strtolower($criteria['email']);
        $entity = $this->findOneBy(array('username' => $criteria['username']));
        $entity_email = null;
        if ($entity === null) {
            $entity_email = $this->findOneBy(array('email' => $criteria['email']));
        }

        if (null === $entity) {
            if ($entity_email !== null) {
                // User added but not contains all fields - need to Update
                $entity = $entity_email;
            } else {
                $entity = new User();
            }
            $entity->setUsername($criteria['username']);
            $entity->setEmail($criteria['email']);
            $entity->setFullName($criteria['fullName']);
            $entity->setLastName($criteria['lastName']);
            $entity->setFirstName($criteria['firstName']);
            $entity->setAnotherId($criteria['anotherId']);
            $entity->setMobile($criteria['mobile']);
            $entity->setIsLdapUser($criteria['ldapUser']);
            $entity->setPassword($criteria['dummyPassword']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }

        return $entity;
    }

    /**
     * @param $email
     * @param $fullName
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createByEmail($email, $fullName): User
    {
        $criteria = array();

        $nameArray = explode(' ', $fullName);
        $firstName = array_shift($nameArray);
        $lastName = implode(' ', $nameArray);
        $criteria['username'] = trim($fullName);
        $criteria['email'] = strtolower(trim($email));
        $criteria['fullName'] = trim($fullName);
        $criteria['lastName'] = trim($lastName);
        $criteria['firstName'] = trim($firstName);
        $criteria['dummyPassword'] = RandomString::generateRandomString(20);

        $entity = $this->findOneBy(array('email' => $criteria['email']));

        if (null === $entity) {
            $entity = new User();
            $entity->setUsername($criteria['username']);
            $entity->setEmail($criteria['email']);
            $entity->setFullName($criteria['fullName']);
            $entity->setLastName($criteria['lastName']);
            $entity->setFirstName($criteria['firstName']);
            $entity->setPassword($criteria['dummyPassword']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }

        return $entity;
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
