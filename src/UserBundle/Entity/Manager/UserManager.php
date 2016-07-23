<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 12:06.
 */
namespace UserBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;
use AppBundle\Entity\TemporaryEntity;
use ChipBundle\DBAL\Types\BatchStatusType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use OrganizationBundle\Entity\Organization;
use UserBundle\Entity\Phone;
use UserBundle\Entity\User;

class UserManager extends BaseEntityManager
{
    /**
     * @param string $phone
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    public function findOneByActivePhone($phone)
    {
        $qb = $this->getRepository()->createQueryBuilder('u')
            ->join('u.phones', 'p')
            ->where('p.phone = :phone')
            ->andWhere(TemporaryEntity::getIsActiveCondition('p'))
            ->setMaxResults(1)
            ->setParameter('phone', $phone)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')));
        try {
            $user = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $user = null;
        }

        return $user;
    }

    public function findOneByActiveChip($uuid)
    {
        $qb = $this->getRepository()->createQueryBuilder('u')
            ->distinct()
            ->addSelect('chips')
            ->join('u.chips', 'chips')
            ->join('chips.batch', 'batches')
            ->where(TemporaryEntity::getIsActiveCondition('chips'))
            ->andWhere('batches.status = :accepted')
            ->andWhere('chips.uuid = :uuid')
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')))
            ->setParameter('accepted', BatchStatusType::ACCEPTED)
            ->setParameter('uuid', $uuid);

        try {
            $user = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $user = null;
        }

        return $user;
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findOneByActiveAccessToken($token)
    {
        if (!$token) {
            return null;
        }
        $qb = $this->getRepository()->createQueryBuilder('u')
            ->addSelect('chips')
            ->join('u.accessTokens', 'at')
            ->leftJoin('u.chips', 'chips')
            ->where('at.token like :token')
            ->andWhere(TemporaryEntity::getIsActiveCondition('at'))
            ->setMaxResults(1)
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UTC')));

        try {
            $user = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $user = null;
        }

        return $user;
    }

    public function findJoinedWithAccounts($id)
    {
        $qb = $this->getRepository()->createQueryBuilder('u')
            ->addSelect(['suaa', 'oaa', 'shaa'])
            ->leftJoin('u.superAdminAccount', 'suaa')
            ->leftJoin('u.organizationAdminAccount', 'oaa')
            ->leftJoin('u.shopAdminAccount', 'shaa')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        try {
            $user = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $user = null;
        }

        return $user;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getUserPromotionUserOrganizationRelationshipQb()
    {
        return $this->getRepository()->createQueryBuilder('user')
            ->join('user.promotions', 'user_promotion')
            ->join('user_promotion.promotion', 'up_promotion')
            ->join('up_promotion.organizationsConfigs', 'up_promotion_configs')
            ->join('up_promotion_configs.organization', 'up_organization');
    }

    /**
     * Returns User with $userId if User has UserPromotion in $organization.
     *
     * @param Organization $organization
     * @param $userId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    public function findUserPromotionUserOrganizationRelationship(Organization $organization, $userId)
    {
        $qb = $this->getUserPromotionUserOrganizationRelationshipQb()
            ->setMaxResults(1)
            ->where('user.id = :user_id')
            ->andWhere('up_organization.id = :organization_id')
            ->setParameters([
                'user_id' => $userId,
                'organization_id' => $organization->getId(),
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getGiftUserOrganizationRelationshipQb()
    {
        return $this->getRepository()->createQueryBuilder('user')
            ->join('user.gifts', 'gift')
            ->join('gift.promotion', 'gift_promotion')
            ->join('gift_promotion.organizationsConfigs', 'gift_promotion_configs')
            ->join('gift_promotion_configs.organization', 'gift_organization');
    }

    /**
     * Returns User with $userId if User has Gift in $organization.
     *
     * @param Organization $organization
     * @param $userId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    public function findGiftUserOrganizationRelationship(Organization $organization, $userId)
    {
        $qb = $this->getGiftUserOrganizationRelationshipQb()
            ->setMaxResults(1)
            ->where('user.id = :user_id')
            ->andWhere('gift_organization.id = :organization_id')
            ->setParameters([
                'user_id' => $userId,
                'organization_id' => $organization->getId(),
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSubscriptionUserOrganizationRelationshipQb()
    {
        return $this->getRepository()->createQueryBuilder('user')
            ->join('user.subscriptions', 'subscription')
            ->join('subscription.organization', 'subscription_organization');
    }

    /**
     * Returns User with $userId if User has UserSubscription on $organization.
     *
     * @param Organization $organization
     * @param $userId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    public function findSubscriptionUserOrganizationRelationship(Organization $organization, $userId)
    {
        $qb = $this->getSubscriptionUserOrganizationRelationshipQb()
            ->setMaxResults(1)
            ->where('user.id = :user_id')
            ->andWhere('subscription_organization.id = :organization_id')
            ->setParameters([
                'user_id' => $userId,
                'organization_id' => $organization->getId(),
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Return user with id = $userId, which is organization client.
     * Organization client is a such user, which has either user_promotion or gift or subscription on this organization.
     *
     * @param Organization $organization
     * @param $userId
     *
     * @return User|null
     */
    public function findOrganizationClient(Organization $organization, $userId)
    {
        //1. User has user_promotion in this organization
        $user = $this->findUserPromotionUserOrganizationRelationship($organization, $userId);
        if ($user) {
            return $user;
        }
        //2. User has gift in this organization
        $user = $this->findGiftUserOrganizationRelationship($organization, $userId);
        if ($user) {
            return $user;
        }
        //3. User subscribed on this organization
        return $this->findSubscriptionUserOrganizationRelationship($organization, $userId);
    }

    /**
     * @param Organization $organization
     *
     * @return array
     */
    public function findOrganizationClients(Organization $organization)
    {
        $subscribers = $this->getSubscriptionUserOrganizationRelationshipQb()
            ->where('subscription_organization.id = :organization_id')
            ->setParameter('organization_id', $organization->getId())
            ->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

        $giftUsers = $this->getGiftUserOrganizationRelationshipQb()
            ->where('gift_organization.id = :organization_id')
            ->setParameter('organization_id', $organization->getId())
            ->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

        $upUsers = $this->getUserPromotionUserOrganizationRelationshipQb()
            ->where('up_organization.id = :organization_id')
            ->setParameter('organization_id', $organization->getId())
            ->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getResult();

        return [
            'gifts' => $giftUsers,
            'promotions' => $upUsers,
            'subscriptions' => $subscribers,
        ];
    }

    /**
     * @param Organization    $organization
     * @param ArrayCollection $tags
     *
     * @return int
     */
    public function distinctCountByOrganizationAndTags(Organization $organization, $tags)
    {
        $qb = $this->getRepository()->createQueryBuilder('user');

        return $qb
            ->select($qb->expr()->countDistinct('user_subscription.user'))
            ->leftJoin('user.subscriptions', 'subscription')
            ->leftJoin('subscription.organization', 'subscription_organization')
            ->leftJoin('user.gifts', 'gift')
            ->leftJoin('gift.promotion', 'gift_promotion')
            ->leftJoin('gift_promotion.organizationsConfigs', 'gift_promotion_configs')
            ->leftJoin('gift_promotion_configs.organization', 'gift_organization')
            ->leftJoin('user.promotions', 'user_promotion')
            ->leftJoin('user_promotion.promotion', 'up_promotion')
            ->leftJoin('up_promotion.organizationsConfigs', 'up_promotion_configs')
            ->leftJoin('up_promotion_configs.organization', 'up_organization')
            ->leftJoin('user.subscriptions', 'user_subscription')
            ->where($qb->expr()->orX(
                $qb->expr()->orX(
                    $qb->expr()->eq('subscription_organization.id', ':organization_id'),
                    $qb->expr()->eq('gift_organization.id', ':organization_id')
                ),
                $qb->expr()->eq('up_organization.id', ':organization_id')
            ))
            ->andWhere($qb->expr()->in('user_subscription.tag', ':tags'))
            ->andWhere(TemporaryEntity::getIsActiveCondition('user_subscription'))
            ->setParameter('organization_id', $organization->getId())
            ->setParameter('tags', $tags)
            ->setParameter('now', new \DateTime(null, new \DateTimeZone('UCT')))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $userId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    public function findOneJoinedWithPhones($userId)
    {
        return $this->getRepository()->createQueryBuilder('user')
            ->addSelect('phones')
            ->join('user.phones', 'phones')
            ->where('user.id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $phone
     *
     * @return \UserBundle\Entity\User
     */
    public function createUserWithPhone($phone)
    {
        $user = new User();
        $userPhone = new Phone();
        $userPhone->setPhone($phone);
        $user->addPhone($userPhone);

        return $user;
    }
}
