<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
// use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function add(Question $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity); 

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Question $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   public function createFilteredQueryBuilder(string $filter): ORMQueryBuilder
   {
        // Doctrine wants us to believe there is a direct connection between TAGS and QUESTIONS
        // to do the join we want the "MANY" tags for this question and let doctrine do the rest
        // SO we do the LEFTJOIN
        
        // check the passed in value. We can also change this so that we are passed a value from a drop down select.
        if ($filter === 'top-rated') {
            $sort = 'q.votes';
            $order = 'DESC';
        } else if ($filter === 'oldest') {
            $sort = 'q.askedAt';
            $order = 'ASC';
        } else if ($filter === 'low-rated') {
            $sort = 'q.votes';
            $order = 'ASC';
        } else {
            $sort = 'q.askedAt';
            $order = 'DESC';
        }

        return $this->addIsAskedQueryBuilder()
            ->orderBy($sort, $order)
            ->leftJoin('q.questionTags', 'question_tag')
            ->innerJoin('question_tag.tag', 'tag') // published questions must have tags
            ->addSelect(['question_tag', 'tag'])
       ;
   }
   // Function to modify passed queryBuilder to accept what we pass in
   private function addIsAskedQueryBuilder(ORMQueryBuilder $qb = null): ORMQueryBuilder
   {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('q.askedAt IS NOT NULL');
   }

   private function getOrCreateQueryBuilder(ORMQueryBuilder $qb = null): ORMQueryBuilder
   {
        // this 'q' is what all of our queries will use to query question columns
        return $qb ?: $this->createQueryBuilder('q');
   }

//    public function findOneBySomeField($value): ?Question
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
