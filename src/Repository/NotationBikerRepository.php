<?php

namespace App\Repository;

use App\Entity\NotationBiker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NotationBiker>
 *
 * @method NotationBiker|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotationBiker|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotationBiker[]    findAll()
 * @method NotationBiker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotationBikerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotationBiker::class);
    }

//    /**
//     * @return NotationBiker[] Returns an array of NotationBiker objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NotationBiker
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
