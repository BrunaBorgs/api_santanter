<?php

namespace App\Repository;

use App\Entity\Transacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transacao>
 */
class TransacaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transacao::class);
    }

    /**
     * 
     * @return Transacao[] Returns an array of Transacao objects
     */
    public function findByContaOrigemOrContaDestino(int $idConta): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.contaOrigem = :id')
            ->orWhere('t.contaDestino = :id')
            ->setParameter('id', $idConta)
            ->orderBy('t.dataHora', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Transacao[] Returns an array of Transacao objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transacao
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
