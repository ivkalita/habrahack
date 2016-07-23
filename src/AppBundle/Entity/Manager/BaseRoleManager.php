<?php

namespace AppBundle\Entity\Manager;

class BaseRoleManager extends BaseEntityManager
{
    public function findAllJoinedWithParents()
    {
        return $this->getRepository()->createQueryBuilder('r')
            ->addSelect('p')
            ->leftJoin('r.parents', 'p')
            ->getQuery()
            ->getResult();
    }
}
