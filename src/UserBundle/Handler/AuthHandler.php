<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 11:57.
 */
namespace UserBundle\Handler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserBundle\Entity\Manager\AccessTokenManager;
use UserBundle\Entity\Manager\DeviceManager;
use UserBundle\Entity\Manager\PhoneManager;
use UserBundle\Entity\Manager\UserManager;

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
     * AuthHandler constructor.
     *
     * @param UserManager              $userManager
     * @param DeviceManager            $deviceManager
     * @param AccessTokenManager       $tokenManager
     * @param UserHandler              $userHandler
     * @param PhoneManager             $phoneManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        UserManager $userManager,
        DeviceManager $deviceManager,
        AccessTokenManager $tokenManager,
        UserHandler $userHandler,
        PhoneManager $phoneManager,
        EventDispatcherInterface $dispatcher
    ) {
        $this->userManager = $userManager;
        $this->deviceManager = $deviceManager;
        $this->tokenManager = $tokenManager;
        $this->userHandler = $userHandler;
        $this->phoneManager = $phoneManager;
        $this->dispatcher = $dispatcher;
    }

//    /**
//     * @param string $phone
//     *
//     * @return Payload
//     */
//    public function request($phone)
//    {
//        $user = $this->userHandler->getOrCreateUserByPhone($phone);
//        $user
//            ->generateSecret()
//            ->generateSmsCode($this->passwordGenerationDisabled);
//
//        $this->userManager->save($user);
//
//        return new Payload([
//            'secret' => $user->getSecret(),
//        ]);
//    }
//
//    /**
//     * @param Confirmation $confirmation
//     *
//     * @throws CredentialsInvalidException
//     * @throws NotFoundException
//     * @throws RequestExpiredException
//     * @throws RequestRequiredException
//     *
//     * @return Payload
//     */
//    public function confirm($confirmation)
//    {
//        $user = $this->userManager->findOneByActivePhone($confirmation->getPhone());
//
//        if (!$user) {
//            throw new NotFoundException([], 'user');
//        }
//        if (!$user->getSmsCode()) {
//            throw new RequestRequiredException();
//        }
//        if ($user->isSmsCodeExpired()) {
//            $user->clearAuthInfo();
//            $this->userManager->save($user);
//            throw new RequestExpiredException();
//        }
//        if (!$user->checkCredentials($confirmation->getPassword())) {
//            throw new CredentialsInvalidException();
//        }
//        //create new token
//        $device = $this->deviceManager->findOneByPlatformAndDeviceId($confirmation->getPlatform(), $confirmation->getDeviceId());
//        if (!$device) {
//            $device = new Device();
//            $device
//                ->setPlatform($confirmation->getPlatform())
//                ->setDeviceId($confirmation->getDeviceId());
//            $this->deviceManager->save($device);
//        }
//        $token = new AccessToken();
//        $token
//            ->setUser($user)
//            ->setDevice($device)
//            ->generateToken();
//
//        $isNew = !$user->isMobileAppInstalled();
//
//        $user->clearAuthInfo();
//        $this->userManager->save($user);
//        $this->tokenManager->save($token);
//
//        return new Payload([
//            'id' => $user->getId(),
//            'is_new' => $isNew,
//            'access_token' => $token->getToken(),
//        ]);
//    }
//
//    /**
//     * @param User  $user
//     * @param Phone $phone
//     *
//     * @throws ActiveChangePhoneRequestFoundException
//     * @throws PhoneAlreadyUsedException
//     * @throws PhonesAreTheSameException
//     *
//     * @return ChangePhoneRequest
//     */
//    public function changePhoneRequest(User $user, Phone $phone)
//    {
//        if ($user->getActivePhone()->getPhone() === $phone->getPhone()) {
//            throw new PhonesAreTheSameException();
//        }
//        $activeChangePhoneRequests = $this->changePhoneRequestManager->findActiveByUser($user);
//        if (count($activeChangePhoneRequests) > 0) {
//            throw new ActiveChangePhoneRequestFoundException();
//        }
//
//        $activePhone = $this->phoneManager->findOneActiveByPhone($phone->getPhone());
//        if (null !== $activePhone) {
//            throw new PhoneAlreadyUsedException();
//        }
//
//        $dt = new \DateTime(null, new \DateTimeZone('UTC'));
//        $dt = $dt->add(new \DateInterval('PT180S'));
//
//        $changePhoneRequest = new ChangePhoneRequest();
//        $changePhoneRequest->setUser($user)
//            ->setNewPhoneString($phone->getPhone())
//            ->setOldPhone($user->getActivePhone())
//            ->generatePasswords($this->passwordGenerationDisabled)
//            ->setUntil($dt);
//
//        $this->changePhoneRequestManager->save($changePhoneRequest);
//
//        $oldPhoneEvent = new ChangePhoneRequestEvent($user, $changePhoneRequest->getRawOldPhonePass(), $user->getActivePhone()->getPhone());
//        $newPhoneEvent = new ChangePhoneRequestEvent($user, $changePhoneRequest->getRawNewPhonePass(), $phone->getPhone());
//        $this->dispatcher->dispatch(ChangePhoneRequestEvent::NAME, $oldPhoneEvent);
//        $this->dispatcher->dispatch(ChangePhoneRequestEvent::NAME, $newPhoneEvent);
//
//        return $changePhoneRequest;
//    }
//
//    /**
//     * @param User                    $user
//     * @param PhoneChangeConfirmation $phoneChangeConfirmation
//     *
//     * @throws ChangePhoneRequestRequiredException
//     * @throws CodesMismatchException
//     * @throws FastAuthenticationFailedException
//     * @throws PhoneAlreadyUsedException
//     *
//     * @return AccessToken
//     */
//    public function confirmPhoneChangeRequest(User $user, PhoneChangeConfirmation $phoneChangeConfirmation)
//    {
//        $activeChangePhoneRequests = $this->changePhoneRequestManager->findActiveByUser($user);
//        if (count($activeChangePhoneRequests) !== 1) {
//            throw new ChangePhoneRequestRequiredException();
//        }
//        $request = $activeChangePhoneRequests[0];
//        $newPhoneString = $request->getNewPhoneString();
//        $activePhone = $this->phoneManager->findOneActiveByPhone($newPhoneString);
//        if (null !== $activePhone) {
//            throw new PhoneAlreadyUsedException();
//        }
//        if (
//            $phoneChangeConfirmation->getOldPhonePass() !== $request->getOldPhonePass() ||
//            $phoneChangeConfirmation->getNewPhonePass() !== $request->getNewPhonePass()
//        ) {
//            throw new CodesMismatchException();
//        }
//        $em = $this->changePhoneRequestManager->getEntityManager();
//        //1. Создаем новый номер телефона пользователя
//        $newPhone = new \UserBundle\Entity\Phone();
//        $newPhone->setUser($user)
//            ->setPhone($newPhoneString);
//        //2. Старый номер телефона делаем неактивным
//        $currentPhone = $user->getActivePhone();
//        $currentPhone->deactivate();
//        //3. Запрос деактивируем
//        $request->setNewPhone($newPhone);
//        $request->deactivate();
//        //4. Сохраняем все это
//        $em->persist($request);
//        $em->persist($currentPhone);
//        $em->persist($newPhone);
//        $em->flush();
//        //5. Деактивируем все активные сессии пользователя
//        $this->tokenManager->deactivateAllForUser($user);
//        //6. Создаем новый токен на основе текущего
//        /**
//         * @var AccessToken $currentToken
//         */
//        $currentToken = $this->tokenManager->findOneBy(['token' => $user->getRequestToken()]);
//        if (!$currentToken) {
//            throw new FastAuthenticationFailedException();
//        }
//        $newToken = new AccessToken();
//        $newToken->setUser($user)
//            ->generateToken()
//            ->setDevice($currentToken->getDevice());
//        $this->tokenManager->save($newToken);
//
//        return $newToken;
//    }
}
