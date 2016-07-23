<?php

namespace UserBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;
use AppBundle\Entity\TemporaryEntity;
use Doctrine\ORM\NoResultException;

class PhoneManager extends BaseEntityManager
{
    public function findOneByPhone($phone)
    {
        $qb = $this->getRepository()->createQueryBuilder('p')
            ->where('p.phone = :phone')
            ->setParameter('phone', $phone)
            ->setMaxResults(1);
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $result = null;
        }

        return $result;
    }

    public function findOneActiveByPhone($phone)
    {
        $qb = $this->getRepository()->createQueryBuilder('p')
            ->where('p.phone = :phone')
            ->andWhere(TemporaryEntity::getIsActiveCondition('p'))
            ->setParameter('phone', $phone)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')))
            ->setMaxResults(1);
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $result = null;
        }

        return $result;
    }
}
