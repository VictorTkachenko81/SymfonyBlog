<?php

namespace AppBundle\Model;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorWithPages extends Paginator
{
    private $currentPage;

    private $countPages;

    public function getCurrentPage()
    {
        $firstResult = $this->getQuery()->getFirstResult();
        $maxResults = $this->getQuery()->getMaxResults();
        $currentPage = $firstResult / $maxResults + 1;

        return $this->currentPage = $currentPage;
    }

    public function getCountPages()
    {
        $countResults = $this->count();
        $maxResults = $this->getQuery()->getMaxResults();
        $countPages = ceil($countResults / $maxResults);

        return $this->countPages = $countPages;
    }
}