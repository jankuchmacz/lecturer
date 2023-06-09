<?php

namespace App\Repository;

use App\Entity\Classes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classes>
 *
 * @method Classes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classes[]    findAll()
 * @method Classes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Classes $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Classes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Classes[] Returns an array of Classes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Classes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findClassesAndStudentsEnrolled($id, $user_id): ?Classes
    {
        return $this->createQueryBuilder('c')->innerJoin('c.students', 's') //assign to classes all students
        ->addSelect('s') //eagerLoading
        ->andWhere('c.id=:id')
        ->setParameter('id', $id)
        ->andWhere('c.user=:userId')
        ->setParameter('userId', $user_id)
        ->getQuery()
        ->getOneOrNullResult();
    }
    public function findClassesOfSpecifiedUser($user_id)
    {
        return $this->createQueryBuilder('c')
        ->andWhere('c.user=:id')
        ->setParameter('id', $user_id)
        ->getQuery()
        ->getResult();
    }
    public function findOneClassesOfSpecifiedUser($id, $user_id): ?Classes
    {
        return $this->createQueryBuilder('c')
        ->andWhere('c.id=:id')
        ->setParameter('id', $id)
        ->andWhere('c.user=:userId')
        ->setParameter('userId', $user_id)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
