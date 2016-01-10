<?php

namespace AppBundle\Repository;
use AppBundle\Model\PaginatorWithPages;
use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
    public function getArticlesWithCountComment($page = 1, $max = 10)
    {
        $first = $max * ($page - 1);
        $query = $this->createQueryBuilder('a')
            ->select('a, c, t, u, count(cm.id) as countComments')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'cm')
            ->join('a.user', 'u')
            ->groupBy('a, c, t, u')
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult($first)
            ->setMaxResults($max)
            ->getQuery();

        return new PaginatorWithPages($query, $fetchJoinCollection = true);
    }

    public function getArticleWithDep($slug)
    {
        return $this->createQueryBuilder('a')
            ->select('a, c, t, u')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.tags', 't')
            ->join('a.user', 'u')
            ->where('a.slug = ?1')
            ->setParameter(1, $slug)
            ->getQuery()
            ->getSingleResult();
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

    public function getArticlesSorted($sortBy, $param, $page = 1, $max = 10)
    {
        $first = $max * ($page - 1);
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

        $query
            ->setFirstResult($first)
            ->setMaxResults($max)
            ->getQuery();

        return new PaginatorWithPages($query, $fetchJoinCollection = true);

    }

    public function getPopularArticles($max = 5)
    {
        return $this->createQueryBuilder('a')
            ->select('a, u, avg(c.rating) as rating')
            ->leftJoin('a.comments', 'c')
            ->join('a.user', 'u')
            ->groupBy('a, u')
            ->orderBy('rating', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    public function getRecentArticles($max = 5)
    {
        return $this->createQueryBuilder('a')
            ->select('a, u')
            ->join('a.user', 'u')
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }
}
