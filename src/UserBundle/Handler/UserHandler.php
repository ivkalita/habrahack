<?php

namespace UserBundle\Handler;

use AppBundle\Classes\Payload;
use AppBundle\Entity\City;
use AppBundle\Entity\Manager\CityManager;
use AppBundle\Exceptions\AccessTokenInvalidException;
use UserBundle\Entity\Manager\AccessTokenManager;
use UserBundle\Entity\Manager\UserManager;
use UserBundle\Model\PlayerId;
use UserBundle\Model\User;

class UserHandler
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var CityManager
     */
    protected $cityManager;

    /**
     * @var AccessTokenManager
     */
    protected $accessTokenManager;

    /**
     * UserHandler constructor.
     *
     * @param UserManager        $userManager
     * @param CityManager        $cityManager
     * @param AccessTokenManager $accessTokenManager
     */
    public function __construct(
        UserManager $userManager,
        CityManager $cityManager,
        AccessTokenManager $accessTokenManager
    ) {
        $this->userManager = $userManager;
        $this->cityManager = $cityManager;
        $this->accessTokenManager = $accessTokenManager;
    }

    /**
     * @param \UserBundle\Entity\User $user
     * @param User                    $newInfo
     *
     * @return Payload
     */
    public function update(\UserBundle\Entity\User $user, User $newInfo)
    {
        /**
         * @var City $city
         */
        $city = $this->cityManager->find($newInfo->getCityId());
        $user->setCity($city)
            ->setFirstName($newInfo->getFirstName())
            ->setMiddleName($newInfo->getMiddleName())
            ->setLastName($newInfo->getLastName());

        $this->userManager->save($user);

        return new Payload($user);
    }

    /**
     * @param $phone
     *
     * @return \UserBundle\Entity\User
     */
    public function getOrCreateUserByPhone($phone)
    {
        $user = $this->userManager->findOneByActivePhone($phone);
        if (null === $user) {
            $user = $this->userManager->createUserWithPhone($phone);
            $this->userManager->save($user);
        }

        return $user;
    }

    /**
     * @param PlayerId                $playerId
     * @param \UserBundle\Entity\User $user
     *
     * @throws AccessTokenInvalidException
     */
    public function addPlayerId(PlayerId $playerId, \UserBundle\Entity\User $user)
    {
        $currentUserAccessToken = $this->accessTokenManager->findOneActiveByUserAndToken($user, $user->getRequestToken());
        if ($currentUserAccessToken === null) {
            throw new AccessTokenInvalidException();
        }
        $currentUserAccessToken->setPlayerId($playerId->getPlayerId());
        $this->accessTokenManager->save($currentUserAccessToken);
    }
}
