<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 23.07.16
 * Time: 19:52.
 */
namespace ArticleBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;
use AppBundle\Exceptions\NotFoundException;
use ArticleBundle\Entity\Article;

class ArticleManager extends BaseEntityManager
{
    /**
     * @param null $fromId
     * @param int  $count
     *
     * @throws NotFoundException
     *
     * @return array
     */
    public function findOrderedResult($fromId = null, $count = 10)
    {
        /**
         * @var Article $pivotArticle
         */
        $pivotArticle = null;
        if ($fromId) {
            $pivotArticle = $this->findOneBy($fromId);
            if (!$pivotArticle) {
                throw new NotFoundException();
            }
        }

        $qb = $this->getRepository()->createQueryBuilder('article')
            ->orderBy('article.createdAt', 'DESC')
            ->setMaxResults($count);

        if ($pivotArticle) {
            $qb->where('article.createdAt < :last_created_at')
                ->setParameter('last_created_at', $pivotArticle->getCreatedAt());
        }

        return $qb->getQuery()->getResult();
    }
}
