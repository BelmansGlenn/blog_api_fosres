<?php

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Interface\SearchInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository implements SearchInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Article $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Article $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function countValue($fields = [])
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('count(a.id)');
            if (count($fields) > 0) {
                foreach ($fields as $key => $value) {
                    $qb->andWhere("a.$key = :key");
                    $qb->setParameter("key", $value);
                }
            }
        return $qb->getQuery()->getSingleScalarResult();
    }


    public function search($term, $order, $limit, $offset, $fields = [])
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->orderBy('a.title', $order);

        if ($term){
            $qb
                ->where('a.title LIKE ?1')
                ->setParameter(1, '%'.$term.'%');
        }
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {

                $qb->andWhere("a.$key = :key");
                $qb->setParameter("key", $value);
            }
        }

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();

    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
