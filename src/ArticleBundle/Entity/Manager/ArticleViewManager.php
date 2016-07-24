<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 24.07.16
 * Time: 0:50.
 */
namespace ArticleBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;

class ArticleViewManager extends BaseEntityManager
{
    public function findLastDayOrderedByCreatedAt()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $yesterday = $now->sub(new \DateInterval('P1D'));
        $qb = $this->getRepository()->createQueryBuilder('view')
            ->where('view.createdAt > :yesterday')
            ->orderBy('view.createdAt', 'ASC')
            ->setParameter('yesterday', $yesterday);

        return $qb->getQuery()->getResult();
    }
}
