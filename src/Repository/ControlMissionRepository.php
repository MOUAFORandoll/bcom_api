<?php

namespace App\Repository;

use App\Entity\ControlMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ControlMission>
 *
 * @method ControlMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method ControlMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method ControlMission[]    findAll()
 * @method ControlMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ControlMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ControlMission::class);
    }

//    /**
//     * @return ControlMission[] Returns an array of ControlMission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ControlMission
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
