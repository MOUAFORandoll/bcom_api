<?php

namespace App\Repository;

use App\Entity\ObjectFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ObjectFile>
 *
 * @method ObjectFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObjectFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObjectFile[]    findAll()
 * @method ObjectFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjectFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjectFile::class);
    }

    //    /**
    //     * @return ObjectFile[] Returns an array of ObjectFile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ObjectFile
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
