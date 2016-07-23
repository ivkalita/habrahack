<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 13.02.16
 * Time: 11:22.
 */
namespace UserBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;
use AppBundle\Entity\TemporaryEntity;
use UserBundle\Entity\AccessToken;
use UserBundle\Entity\User;

class AccessTokenManager extends BaseEntityManager
{
    /**
     * @param $user
     *
     * @return AccessToken[]
     */
    public function findActiveByUser($user)
    {
        return $this->getRepository()->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere(TemporaryEntity::getIsActiveCondition('a'))
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AccessToken|string $token
     */
    public function deactivateToken($token)
    {
        $accessToken = $token instanceof AccessToken ? $token : $this->findOneBy(['token' => $token]);
        $this->save($accessToken->deactivate());
    }

    /**
     * @param User $user
     */
    public function deactivateAllForUser(User $user)
    {
        $tokens = $this->findActiveByUser($user);
        foreach ($tokens as $token) {
            $this->save($token->deactivate(), false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     * @param $token
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return AccessToken
     */
    public function findOneActiveByUserAndToken(User $user, $token)
    {
        return $this->getRepository()->createQueryBuilder('access_token')
            ->where('access_token.user = :user')
            ->andWhere('access_token.token = :token')
            ->andWhere(TemporaryEntity::getIsActiveCondition('access_token'))
            ->setParameter('user', $user)
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
