<?php

namespace App\Repository;

use App\Entity\MissionSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MissionSession>
 *
 * @method MissionSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method MissionSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method MissionSession[]    findAll()
 * @method MissionSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissionSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissionSession::class);
    }

    //    /**
    //     * @return MissionSession[] Returns an array of MissionSession objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MissionSession
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
