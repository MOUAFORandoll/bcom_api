<?php

namespace App\Repository;

use App\Entity\DisponibiliteBiker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisponibiliteBiker>
 *
 * @method DisponibiliteBiker|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisponibiliteBiker|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisponibiliteBiker[]    findAll()
 * @method DisponibiliteBiker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibiliteBikerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisponibiliteBiker::class);
    }

    //    /**
    //     * @return DisponibiliteBiker[] Returns an array of DisponibiliteBiker objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    // Dans DisponibiliteBikerRepository

    public function findLatestActiveByBiker($biker)
    {
        return $this->createQueryBuilder('d')
            ->where('d.biker = :biker')
            ->andWhere('d.status = true') // Assurez-vous que la disponibilité est active
            ->setParameter('biker', $biker)
            ->orderBy('d.start_dispo', 'DESC') // ou utilisez 'd.createdAt' si disponible
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    public function findOneBySomeField($value): ?DisponibiliteBiker
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
