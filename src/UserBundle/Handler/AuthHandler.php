<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 11:57.
 */
namespace UserBundle\Handler;

use AppBundle\Classes\Payload;
use AppBundle\Exceptions\NotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserBundle\Entity\AccessToken;
use UserBundle\Entity\ChangePhoneRequest;
use UserBundle\Entity\Device;
use UserBundle\Entity\Manager\AccessTokenManager;
use UserBundle\Entity\Manager\ChangePhoneRequestManager;
use UserBundle\Entity\Manager\DeviceManager;
use UserBundle\Entity\Manager\PhoneManager;
use UserBundle\Entity\Manager\UserManager;
use UserBundle\Entity\Manager\UserRoleManager;
use UserBundle\Entity\User;
use UserBundle\Event\ChangePhoneRequestEvent;
use UserBundle\Event\UserAuthorizationEvent;
use UserBundle\Exceptions\Auth\CredentialsInvalidException;
use UserBundle\Exceptions\Auth\RequestExpiredException;
use UserBundle\Exceptions\Auth\RequestRequiredException;
use UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException;
use UserBundle\Exceptions\ChangePhone\ChangePhoneRequestRequiredException;
use UserBundle\Exceptions\ChangePhone\CodesMismatchException;
use UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException;
use UserBundle\Exceptions\ChangePhone\PhoneAlreadyUsedException;
use UserBundle\Exceptions\ChangePhone\PhonesAreTheSameException;
use UserBundle\Model\Confirmation;
use UserBundle\Model\Phone;
use UserBundle\Model\PhoneChangeConfirmation;

class AuthHandler
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var DeviceManager
     */
    protected $deviceManager;

    /**
     * @var AccessTokenManager
     */
    protected $tokenManager;

    /**
     * @var ChangePhoneRequestManager
     */
    protected $changePhoneRequestManager;

    /**
     * @var PhoneManager
     */
    protected $phoneManager;

    /**
     * @var UserHandler
     */
    protected $userHandler;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var bool
     */
    protected $passwordGenerationDisabled;

    /**
     * AuthHandler constructor.
     *
     * @param UserManager               $userManager
     * @param DeviceManager             $deviceManager
     * @param AccessTokenManager        $tokenManager
     * @param UserRoleManager           $userRoleManager
     * @param UserHandler               $userHandler
     * @param ChangePhoneRequestManager $changePhoneRequestManager
     * @param PhoneManager              $phoneManager
     * @param EventDispatcherInterface  $dispatcher
     * @param bool                      $passwordGenerationDisabled
     */
    public function __construct(
        UserManager $userManager,
        DeviceManager $deviceManager,
        AccessTokenManager $tokenManager,
        UserRoleManager $userRoleManager,
        UserHandler $userHandler,
        ChangePhoneRequestManager $changePhoneRequestManager,
        PhoneManager $phoneManager,
        EventDispatcherInterface $dispatcher,
        $passwordGenerationDisabled
    ) {
        $this->userManager = $userManager;
        $this->deviceManager = $deviceManager;
        $this->tokenManager = $tokenManager;
        $this->userRoleManager = $userRoleManager;
        $this->userHandler = $userHandler;
        $this->changePhoneRequestManager = $changePhoneRequestManager;
        $this->phoneManager = $phoneManager;
        $this->dispatcher = $dispatcher;
        $this->passwordGenerationDisabled = $passwordGenerationDisabled;
    }

    /**
     * @param string $phone
     *
     * @return Payload
     */
    public function request($phone)
    {
        $user = $this->userHandler->getOrCreateUserByPhone($phone);
        $user
            ->generateSecret()
            ->generateSmsCode($this->passwordGenerationDisabled);

        $this->userManager->save($user);

        $event = new UserAuthorizationEvent($user, $user->getSmsCode(), $phone);
        $this->dispatcher->dispatch(UserAuthorizationEvent::NAME, $event);

        return new Payload([
            'secret' => $user->getSecret(),
        ]);
    }

    /**
     * @param Confirmation $confirmation
     *
     * @throws CredentialsInvalidException
     * @throws NotFoundException
     * @throws RequestExpiredException
     * @throws RequestRequiredException
     *
     * @return Payload
     */
    public function confirm($confirmation)
    {
        $user = $this->userManager->findOneByActivePhone($confirmation->getPhone());

        if (!$user) {
            throw new NotFoundException([], 'user');
        }
        if (!$user->getSmsCode()) {
            throw new RequestRequiredException();
        }
        if ($user->isSmsCodeExpired()) {
            $user->clearAuthInfo();
            $this->userManager->save($user);
            throw new RequestExpiredException();
        }
        if (!$user->checkCredentials($confirmation->getPassword())) {
            throw new CredentialsInvalidException();
        }
        //create new token
        $device = $this->deviceManager->findOneByPlatformAndDeviceId($confirmation->getPlatform(), $confirmation->getDeviceId());
        if (!$device) {
            $device = new Device();
            $device
                ->setPlatform($confirmation->getPlatform())
                ->setDeviceId($confirmation->getDeviceId());
            $this->deviceManager->save($device);
        }
        $token = new AccessToken();
        $token
            ->setUser($user)
            ->setDevice($device)
            ->generateToken();

        $role = $this->userRoleManager->findOneByRole('USER');

        $isNew = !$user->isMobileAppInstalled();
        if ($isNew && $role) {
            $user->addUserRole($role);
        }

        $user->clearAuthInfo();
        $this->userManager->save($user);
        $this->tokenManager->save($token);

        return new Payload([
            'id' => $user->getId(),
            'is_new' => $isNew,
            'access_token' => $token->getToken(),
        ]);
    }

    /**
     * @param User  $user
     * @param Phone $phone
     *
     * @throws ActiveChangePhoneRequestFoundException
     * @throws PhoneAlreadyUsedException
     * @throws PhonesAreTheSameException
     *
     * @return ChangePhoneRequest
     */
    public function changePhoneRequest(User $user, Phone $phone)
    {
        if ($user->getActivePhone()->getPhone() === $phone->getPhone()) {
            throw new PhonesAreTheSameException();
        }
        $activeChangePhoneRequests = $this->changePhoneRequestManager->findActiveByUser($user);
        if (count($activeChangePhoneRequests) > 0) {
            throw new ActiveChangePhoneRequestFoundException();
        }

        $activePhone = $this->phoneManager->findOneActiveByPhone($phone->getPhone());
        if (null !== $activePhone) {
            throw new PhoneAlreadyUsedException();
        }

        $dt = new \DateTime(null, new \DateTimeZone('UTC'));
        $dt = $dt->add(new \DateInterval('PT180S'));

        $changePhoneRequest = new ChangePhoneRequest();
        $changePhoneRequest->setUser($user)
            ->setNewPhoneString($phone->getPhone())
            ->setOldPhone($user->getActivePhone())
            ->generatePasswords($this->passwordGenerationDisabled)
            ->setUntil($dt);

        $this->changePhoneRequestManager->save($changePhoneRequest);

        $oldPhoneEvent = new ChangePhoneRequestEvent($user, $changePhoneRequest->getRawOldPhonePass(), $user->getActivePhone()->getPhone());
        $newPhoneEvent = new ChangePhoneRequestEvent($user, $changePhoneRequest->getRawNewPhonePass(), $phone->getPhone());
        $this->dispatcher->dispatch(ChangePhoneRequestEvent::NAME, $oldPhoneEvent);
        $this->dispatcher->dispatch(ChangePhoneRequestEvent::NAME, $newPhoneEvent);

        return $changePhoneRequest;
    }

    /**
     * @param User                    $user
     * @param PhoneChangeConfirmation $phoneChangeConfirmation
     *
     * @throws ChangePhoneRequestRequiredException
     * @throws CodesMismatchException
     * @throws FastAuthenticationFailedException
     * @throws PhoneAlreadyUsedException
     *
     * @return AccessToken
     */
    public function confirmPhoneChangeRequest(User $user, PhoneChangeConfirmation $phoneChangeConfirmation)
    {
        $activeChangePhoneRequests = $this->changePhoneRequestManager->findActiveByUser($user);
        if (count($activeChangePhoneRequests) !== 1) {
            throw new ChangePhoneRequestRequiredException();
        }
        $request = $activeChangePhoneRequests[0];
        $newPhoneString = $request->getNewPhoneString();
        $activePhone = $this->phoneManager->findOneActiveByPhone($newPhoneString);
        if (null !== $activePhone) {
            throw new PhoneAlreadyUsedException();
        }
        if (
            $phoneChangeConfirmation->getOldPhonePass() !== $request->getOldPhonePass() ||
            $phoneChangeConfirmation->getNewPhonePass() !== $request->getNewPhonePass()
        ) {
            throw new CodesMismatchException();
        }
        $em = $this->changePhoneRequestManager->getEntityManager();
        //1. Создаем новый номер телефона пользователя
        $newPhone = new \UserBundle\Entity\Phone();
        $newPhone->setUser($user)
            ->setPhone($newPhoneString);
        //2. Старый номер телефона делаем неактивным
        $currentPhone = $user->getActivePhone();
        $currentPhone->deactivate();
        //3. Запрос деактивируем
        $request->setNewPhone($newPhone);
        $request->deactivate();
        //4. Сохраняем все это
        $em->persist($request);
        $em->persist($currentPhone);
        $em->persist($newPhone);
        $em->flush();
        //5. Деактивируем все активные сессии пользователя
        $this->tokenManager->deactivateAllForUser($user);
        //6. Создаем новый токен на основе текущего
        /**
         * @var AccessToken $currentToken
         */
        $currentToken = $this->tokenManager->findOneBy(['token' => $user->getRequestToken()]);
        if (!$currentToken) {
            throw new FastAuthenticationFailedException();
        }
        $newToken = new AccessToken();
        $newToken->setUser($user)
            ->generateToken()
            ->setDevice($currentToken->getDevice());
        $this->tokenManager->save($newToken);

        return $newToken;
    }
}
