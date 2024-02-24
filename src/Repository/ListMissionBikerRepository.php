<?php

namespace App\Repository;

use App\Entity\ListMissionBiker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListMissionBiker>
 *
 * @method ListMissionBiker|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListMissionBiker|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListMissionBiker[]    findAll()
 * @method ListMissionBiker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListMissionBikerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListMissionBiker::class);
    }

//    /**
//     * @return ListMissionBiker[] Returns an array of ListMissionBiker objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListMissionBiker
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
