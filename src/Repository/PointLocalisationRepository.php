<?php

namespace App\Repository;

use App\Entity\PointLocalisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PointLocalisation>
 *
 * @method PointLocalisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PointLocalisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PointLocalisation[]    findAll()
 * @method PointLocalisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointLocalisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointLocalisation::class);
    }

    public function save(PointLocalisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PointLocalisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByname($searchTerm): array
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.libelle) LIKE :searchTermLower')
            ->orWhere('UPPER(u.libelle) LIKE :searchTermUpper')
            ->setParameter('searchTermLower', '%' . strtolower($searchTerm) . '%')
            ->setParameter('searchTermUpper', '%' . strtoupper($searchTerm) . '%')
            ->getQuery()
            ->getResult();
    }
    public function findOneByName($searchTerm)
{
    return $this->createQueryBuilder('u')
        ->where('LOWER(u.libelle) LIKE :searchTermLower')
        ->orWhere('UPPER(u.libelle) LIKE :searchTermUpper')
        ->setParameter('searchTermLower', '%' . strtolower($searchTerm) . '%')
        ->setParameter('searchTermUpper', '%' . strtoupper($searchTerm) . '%')
        ->getQuery()
        ->getOneOrNullResult();
}
    
    //    /**
    //     * @return PointLocalisation[] Returns an array of PointLocalisation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PointLocalisation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
