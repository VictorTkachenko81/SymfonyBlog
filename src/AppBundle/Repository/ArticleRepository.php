<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    public function getArticlesWithDep()
    {
        return $this->createQueryBuilder('a')
            ->select('a, c, t, u, cm')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'cm')
            ->join('a.user', 'u')
            ->getQuery()
            ->getResult();
    }

    public function getArticlesWithCountComment()
    {
        return $this->createQueryBuilder('a')
            ->select('a, c, t, u, count(cm.id) as countComments')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'cm')
            ->join('a.user', 'u')
            ->groupBy('a, c, t, u')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getArticleWithCountComment($slug)
    {
        return $this->createQueryBuilder('a')
            ->select('a, c, t, u, count(cm.id) as countComments')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'cm')
            ->join('a.user', 'u')
            ->where('a.slug = ?1')
            ->groupBy('a, c, t, u')
            ->setParameter(1, $slug)
            ->getQuery()
            ->getSingleResult();
    }

    public function getArticlesSorted($sortBy, $param)
    {
        $query = $this->createQueryBuilder('a')
            ->select('a, c, t, u, count(cm.id) as countComments')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'cm')
            ->join('a.user', 'u')
            ->groupBy('a, c, t, u')
            ->orderBy('a.createdAt', 'DESC');

        switch ($sortBy) {
            case 'category':
                $query
                    ->where('c.slug = ?1')
                    ->setParameter(1, $param);
                break;
            case 'tag':
                $query
                    ->where('t.slug = ?1')
                    ->setParameter(1, $param);
                break;
            case 'author':
                $query
                    ->where('u.slug = ?1')
                    ->setParameter(1, $param);
                break;
            case 'date':
                $query
                    ->where('a.createdAt >= ?1')
                    ->andWhere('a.createdAt <= ?2')
                    ->setParameter(1, $param." 00:00:00")
                    ->setParameter(2, $param." 23:59:59");
                break;
        }

        return $query
            ->getQuery()
            ->getResult();

    }
}
