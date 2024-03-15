<?php

namespace App\Repository;

use App\Entity\InfoBiker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoBiker>
 *
 * @method InfoBiker|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoBiker|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoBiker[]    findAll()
 * @method InfoBiker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoBikerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoBiker::class);
    }

    //    /**
    //     * @return InfoBiker[] Returns an array of InfoBiker objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?InfoBiker
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
