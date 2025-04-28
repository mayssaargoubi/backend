<?php

namespace App\Repository;

use App\Entity\ObjectifNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ObjectifNote>
 */
class ObjectifNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjectifNote::class);
    }

    //    /**
    //     * @return ObjectifNote[] Returns an array of ObjectifNote objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ObjectifNote
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByEvaluationPeriod($employee, \DateTimeInterface $from, \DateTimeInterface $to): array
{
    return $this->createQueryBuilder('n')
        ->join('n.evaluation', 'e')
        ->andWhere('e.employee = :employee')
        ->andWhere('e.dateEvaluation BETWEEN :from AND :to')
        ->setParameter('employee', $employee)
        ->setParameter('from', $from)
        ->setParameter('to', $to)
        ->getQuery()
        ->getResult();
}

}
