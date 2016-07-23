<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 30.03.16
 * Time: 11:33.
 */
namespace UserBundle\Tests\Handler;

use AppBundle\Exceptions\NotFoundException;
use AppBundle\Tests\BaseServiceTestCase;
use UserBundle\DBAL\Types\PlatformType;
use UserBundle\Entity\AccessToken;
use UserBundle\Entity\ChangePhoneRequest;
use UserBundle\Entity\Device;
use UserBundle\Entity\User;
use UserBundle\Exceptions\Auth\CredentialsInvalidException;
use UserBundle\Exceptions\Auth\RequestExpiredException;
use UserBundle\Exceptions\Auth\RequestRequiredException;
use UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException;
use UserBundle\Exceptions\ChangePhone\ChangePhoneRequestRequiredException;
use UserBundle\Exceptions\ChangePhone\CodesMismatchException;
use UserBundle\Exceptions\ChangePhone\PhoneAlreadyUsedException;
use UserBundle\Exceptions\ChangePhone\PhonesAreTheSameException;
use UserBundle\Handler\AuthHandler;
use UserBundle\Model\Confirmation;
use UserBundle\Model\Phone;
use UserBundle\Model\PhoneChangeConfirmation;

class AuthHandlerTest extends BaseServiceTestCase
{
    /**
     * @var AuthHandler
     */
    protected static $authHandler;

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$authHandler = static::$container->get('user.handler.auth');
        static::$baseFixturesPath = '@UserBundle/DataFixtures/ORM/Handler/AuthHandler';
    }

    /**
     * @param User|null $user
     *
     * @return Confirmation
     */
    protected function createConfirmationFromUser(User $user = null)
    {
        $phone = $user ? $user->getActivePhone() : '+70000000000';
        $deviceId = 'some-device-id';
        $platform = PlatformType::IOS;
        $pass = $user ? hash('sha256', $user->getSmsCode() . $user->getSecret()) : 'some-pass';
        $confirmation = new Confirmation();
        $confirmation
            ->setPhone($phone)
            ->setDeviceId($deviceId)
            ->setPlatform($platform)
            ->setPassword($pass);

        return $confirmation;
    }

    /**
     * ECP-156 bug test.
     */
    public function testConfirmExistedUserSuccess()
    {
        $this->loadTestBasedFixture('existed_user_first_login.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = $this->createConfirmationFromUser($user);
        try {
            $result = static::$authHandler->confirm($confirmation);
            $this->assertEquals($result->get('id'), $user->getId());
            $this->assertTrue($result->get('is_new'));
            $this->assertNotNull($result->get('access_token'));
        } catch (\Exception $e) {
            $this->fail('Unexpected exception');
        }
    }

    public function testConfirmNoUserFail()
    {
        $this->loadTestBasedFixture();
        $confirmation = $this->createConfirmationFromUser();
        try {
            static::$authHandler->confirm($confirmation);
            $this->fail('Exception was not thrown');
        } catch (\Exception $e) {
            if (!$e instanceof NotFoundException) {
                $this->fail('Expected NotFoundException');
            }
        }
    }

    public function testConfirmUserWithoutRequestFail()
    {
        $this->loadTestBasedFixture('user_without_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = $this->createConfirmationFromUser($user);
        try {
            static::$authHandler->confirm($confirmation);
            $this->fail('Exception was not thrown');
        } catch (\Exception $e) {
            if (!$e instanceof RequestRequiredException) {
                $this->fail('Expected RequestRequiredException');
            }
        }
    }

    public function testConfirmUserWithExpiredSmsCodeFail()
    {
        $this->loadTestBasedFixture('user_with_expired_sms_code.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = $this->createConfirmationFromUser($user);
        try {
            static::$authHandler->confirm($confirmation);
            $this->fail('Exception was not thrown');
        } catch (\Exception $e) {
            if (!$e instanceof RequestExpiredException) {
                $this->fail('Expected RequestExpiredException');
            }
        }
    }

    public function testConfirmInvalidCredentialsFail()
    {
        $this->loadTestBasedFixture('user_with_auth_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = $this->createConfirmationFromUser($user);
        $confirmation->setPassword('pass');
        try {
            static::$authHandler->confirm($confirmation);
            $this->fail('Exception was not thrown');
        } catch (\Exception $e) {
            if (!$e instanceof CredentialsInvalidException) {
                $this->fail('Expected CredentialsInvalidException');
            }
        }
    }

    public function testConfirmSuccess()
    {
        $this->loadTestBasedFixture('user_with_auth_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = $this->createConfirmationFromUser($user);
        try {
            $result = static::$authHandler->confirm($confirmation);
            $this->assertEquals($result->get('id'), $user->getId());
            $this->assertTrue($result->get('is_new'));
            $this->assertNotNull($result->get('access_token'));
        } catch (\Exception $e) {
            $this->fail('Unexpected exception');
        }
    }

    public function testRequestNewUserSuccess()
    {
        $this->loadTestBasedFixture();
        $userPhone = '+70000000000';
        try {
            $result = static::$authHandler->request($userPhone);
            $this->assertNotNull($result->get('secret'));
        } catch (\Exception $e) {
            $this->fail('Unexpected exception');
        }
    }

    public function testRequestExistedUserSuccess()
    {
        $this->loadTestBasedFixture('user_without_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        try {
            $result = static::$authHandler->request($user->getActivePhone()->getPhone());
            $this->assertNotNull($result->get('secret'));
            $this->assertEquals($user->getSecret(), $result->get('secret'));
        } catch (\Exception $e) {
            $this->fail('Unexpected exception');
        }
    }

    /**
     * Пользователь пытается сменить номер телефона
     * на тот, который является его активным номером телефона.
     *
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException
     */
    public function testPhoneChangeRequestSamePhone()
    {
        $this->loadTestBasedFixture('user.yml');

        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $phoneModel = new Phone();
        $phoneModel->setPhone($user->getActivePhone());
        try {
            static::$authHandler->changePhoneRequest($user, $phoneModel);
            $this->fail('Exception was not thrown');
        } catch (PhonesAreTheSameException $e) {
        }
    }

    /**
     * Пользователь пытается сменить номер телефона на тот,
     * который уже существует и является активным.
     *
     * @throws PhonesAreTheSameException
     * @throws \UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException
     */
    public function testPhoneChangeRequestActiveUserPhoneExists()
    {
        $this->loadTestBasedFixture('users.yml');
        /**
         * @var User $user1
         * @var User $user2
         */
        $user1 = $this->fixtures['user__1'];
        $user2 = $this->fixtures['user__2'];
        $phoneModel = new Phone();
        $phoneModel->setPhone($user2->getActivePhone());
        try {
            static::$authHandler->changePhoneRequest($user1, $phoneModel);
            $this->fail('Exception was not thrown');
        } catch (PhoneAlreadyUsedException $e) {
        }
    }

    /**
     * @param ChangePhoneRequest       $changePhoneRequest
     * @param User                     $user
     * @param \UserBundle\Entity\Phone $oldPhone
     * @param $newPhoneString
     */
    private function checkChangePhoneRequestPrepared(
        ChangePhoneRequest $changePhoneRequest,
        User $user,
        \UserBundle\Entity\Phone $oldPhone,
        $newPhoneString
    ) {
        $this->assertEquals($changePhoneRequest->getNewPhoneString(), $newPhoneString);
        $this->assertEquals($changePhoneRequest->getUser()->getId(), $user->getId());
        $this->assertEquals($changePhoneRequest->getOldPhone()->getId(), $oldPhone->getId());
        $this->assertNotNull($changePhoneRequest->getOldPhonePass());
        $this->assertNotNull($changePhoneRequest->getNewPhonePass());
        $this->assertNotEquals($changePhoneRequest->getOldPhonePass(), $changePhoneRequest->getNewPhonePass());
        $since = $changePhoneRequest->getSince();
        $until = $changePhoneRequest->getUntil();
        $this->assertNotNull($since);
        $this->assertNotNull($until);
        $checkUntil = clone $since;
        $checkUntilTs = $checkUntil->getTimestamp();
        $checkUntil->setTimestamp($checkUntilTs + 60 * 3);
        $this->assertEquals($until, $checkUntil);
    }

    /**
     * Пользователь пытается сменить номер на тот,
     * который существует, но уже не активен.
     *
     * @throws PhoneAlreadyUsedException
     * @throws PhonesAreTheSameException
     * @throws \UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException
     */
    public function testPhoneChangeRequestInactiveUserPhoneExists()
    {
        $this->loadTestBasedFixture('user_with_active_and_inactive_phones.yml');
        /**
         * @var User                     $user
         * @var \UserBundle\Entity\Phone $inactivePhone
         * @var \UserBundle\Entity\Phone $oldPhone
         */
        $user = $this->fixtures['user'];
        $inactivePhone = $this->fixtures['phone__2'];
        $oldPhone = $this->fixtures['phone__1'];
        $phoneModel = new Phone();
        $phoneModel->setPhone($inactivePhone->getPhone());

        $changePhoneRequest = static::$authHandler->changePhoneRequest($user, $phoneModel);
        $this->checkChangePhoneRequestPrepared($changePhoneRequest, $user, $oldPhone, $phoneModel->getPhone());
    }

    /**
     * Пользователь пытается сменить номер, но уже существует
     * активный запрос на смену номера телефона.
     *
     * @throws PhoneAlreadyUsedException
     * @throws PhonesAreTheSameException
     */
    public function testPhoneChangeRequestActiveRequestExists()
    {
        $this->loadTestBasedFixture('user_with_active_change_phone_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $phoneModel = new Phone();
        $phoneModel->setPhone('+70000000002');

        try {
            static::$authHandler->changePhoneRequest($user, $phoneModel);
            $this->fail('Exception was not thrown');
        } catch (ActiveChangePhoneRequestFoundException $e) {
        }
    }

    /**
     * Пользователь пытается сменить номер, и у него существует
     * неактивный запрос на смену номера телефона.
     *
     * @throws ActiveChangePhoneRequestFoundException
     * @throws PhoneAlreadyUsedException
     * @throws PhonesAreTheSameException
     */
    public function testPhoneChangeRequestInactiveRequestExists()
    {
        $this->loadTestBasedFixture('user_with_inactive_change_phone_request.yml');
        /**
         * @var User                     $user
         * @var \UserBundle\Entity\Phone $oldPhone
         */
        $user = $this->fixtures['user'];
        $oldPhone = $this->fixtures['phone'];
        $phoneModel = new Phone();
        $phoneModel->setPhone('+70000000002');

        $chanePhoneRequest = static::$authHandler->changePhoneRequest($user, $phoneModel);
        $this->checkChangePhoneRequestPrepared($chanePhoneRequest, $user, $oldPhone, $phoneModel->getPhone());
    }

    /**
     * Пользователь пытается сменить номер телефона.
     *
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\ActiveChangePhoneRequestFoundException
     */
    public function testPhoneChangeRequestSuccess()
    {
        $this->loadTestBasedFixture('user.yml');

        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $oldPhone = $this->fixtures['phone'];
        $phoneModel = new Phone();
        $phoneModel->setPhone('+79000000000');
        $changePhoneRequest = static::$authHandler->changePhoneRequest($user, $phoneModel);

        $this->checkChangePhoneRequestPrepared($changePhoneRequest, $user, $oldPhone, $phoneModel->getPhone());
    }

    /**
     * Пользователь пытается подтвердить смену номера телефона,
     * но у него нет запроса на смену номер телефона.
     *
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\CodesMismatchException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmWithoutRequest()
    {
        $this->loadTestBasedFixture('user.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setNewPhonePass('1')
            ->setOldPhonePass('2');
        try {
            static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);
            $this->fail('Exception was not thrown');
        } catch (ChangePhoneRequestRequiredException $e) {
        }
    }

    /**
     * Пользователь пытается подтвердить смену номера телефона,
     * но его запрос смены номера телефона истёк.
     *
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\CodesMismatchException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmWithOutdatedRequest()
    {
        $this->loadTestBasedFixture('user_with_inactive_change_phone_request.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user'];
        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setNewPhonePass('1')
            ->setOldPhonePass('2');
        try {
            static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);
            $this->fail('Exception was not thrown');
        } catch (ChangePhoneRequestRequiredException $e) {
        }
    }

    /**
     * Пользователь пытается подтвердить смену номера телефона,
     * он отправил запрос на смену номера телефона,
     * но перед тем, как он его подтвердил, пользователь user__2
     * зарегистрировался под тем номером телефона, на который первый пользователь
     * хотел поменять свой номер
     *
     * @throws ChangePhoneRequestRequiredException
     * @throws \UserBundle\Exceptions\ChangePhone\CodesMismatchException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmWithAnotherUserActivePhone()
    {
        $this->loadTestBasedFixture('user_with_change_phone_request_and_another_user_active_phone.yml');
        /**
         * @var User $user
         */
        $user = $this->fixtures['user__1'];
        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setNewPhonePass('1')
            ->setOldPhonePass('2');
        try {
            static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);
            $this->fail('Exception was not thrown');
        } catch (PhoneAlreadyUsedException $e) {
        }
    }

    /**
     * Пользователь пытается подтвердить запрос на смену номера телефона,
     * используя неверный код для старого номера телефона.
     *
     * @throws ChangePhoneRequestRequiredException
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmWithInvalidOldPass()
    {
        $this->loadTestBasedFixture('user_with_active_change_phone_request.yml');
        /**
         * @var User               $user
         * @var ChangePhoneRequest $changePhoneRequest
         */
        $user = $this->fixtures['user'];
        $changePhoneRequest = $this->fixtures['change_phone_request'];
        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setOldPhonePass($changePhoneRequest->getOldPhonePass() . 'a')
            ->setNewPhonePass($changePhoneRequest->getNewPhonePass());
        try {
            static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);
            $this->fail('Exception was not thrown');
        } catch (CodesMismatchException $e) {
        }
    }

    /**
     * Пользователь пытается подтвердить запрос на смену номера телефона,
     * используя неверный код для нового номера телефона.
     *
     * @throws ChangePhoneRequestRequiredException
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmWithInvalidNewPass()
    {
        $this->loadTestBasedFixture('user_with_active_change_phone_request.yml');
        /**
         * @var User               $user
         * @var ChangePhoneRequest $changePhoneRequest
         */
        $user = $this->fixtures['user'];
        $changePhoneRequest = $this->fixtures['change_phone_request'];
        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setOldPhonePass($changePhoneRequest->getOldPhonePass())
            ->setNewPhonePass($changePhoneRequest->getNewPhonePass() . 'a');
        try {
            static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);
            $this->fail('Exception was not thrown');
        } catch (CodesMismatchException $e) {
        }
    }

    /**
     * Пользователь успешно подтверждает запрос на смену номера телефона.
     *
     * @throws ChangePhoneRequestRequiredException
     * @throws CodesMismatchException
     * @throws PhoneAlreadyUsedException
     * @throws \UserBundle\Exceptions\ChangePhone\FastAuthenticationFailedException
     */
    public function testPhoneChangeConfirmSuccess()
    {
        $this->loadTestBasedFixture('user_with_active_change_phone_request_and_tokens.yml');
        /**
         * @var User                     $user
         * @var Device                   $currentDevice
         * @var AccessToken              $currentToken
         * @var AccessToken              $anotherToken
         * @var \UserBundle\Entity\Phone $oldPhone
         * @var ChangePhoneRequest       $changePhoneRequest
         */
        $user = $this->fixtures['user'];
        $currentDevice = $this->fixtures['device__current'];
        $currentToken = $this->fixtures['access_token__current'];
        $anotherToken = $this->fixtures['access_token__another'];
        $oldPhone = $this->fixtures['phone'];
        $changePhoneRequest = $this->fixtures['change_phone_request'];
        //Это действие должно происходить при аутентификации
        $user->setRequestToken($currentToken->getToken());

        $confirmation = new PhoneChangeConfirmation();
        $confirmation
            ->setOldPhonePass($changePhoneRequest->getOldPhonePass())
            ->setNewPhonePass($changePhoneRequest->getNewPhonePass());
        $accessToken = static::$authHandler->confirmPhoneChangeRequest($user, $confirmation);

        $em = static::$container->get('doctrine')->getManager();
        //1. Проверим, что у пользователя сменился номер
        /**
         * @var \UserBundle\Entity\Phone[] $phones
         */
        $phones = $em->getRepository('UserBundle:Phone')->findBy(['user' => $user]);
        $this->assertCount(2, $phones);
        $activePhone = $inactivePhone = null;
        if ($phones[0]->isActual()) {
            $activePhone = $phones[0];
            $inactivePhone = $phones[1];
        } elseif ($phones[1]->isActual()) {
            $activePhone = $phones[1];
            $inactivePhone = $phones[0];
        } else {
            $this->fail('No active phones found for user');
        }
        $this->assertFalse($inactivePhone->isActual());
        $this->assertEquals($oldPhone->getPhone(), $inactivePhone->getPhone());
        $this->assertEquals($oldPhone->getId(), $inactivePhone->getId());
        $this->assertEquals($changePhoneRequest->getNewPhoneString(), $activePhone->getPhone());
        $this->assertEquals($changePhoneRequest->getNewPhoneString(), $changePhoneRequest->getNewPhone()->getPhone());
        $this->assertEquals($activePhone->getId(), $changePhoneRequest->getNewPhone()->getId());
        $this->assertEquals($changePhoneRequest->getOldPhone()->getId(), $oldPhone->getId());

        //2. Проверим, что все токены пользователя деактивированы
        $currentToken = $em->getRepository('UserBundle:AccessToken')->find($currentToken->getId());
        $anotherToken = $em->getRepository('UserBundle:AccessToken')->find($anotherToken->getId());
        $this->assertFalse($currentToken->isActual());
        $this->assertFalse($anotherToken->isActual());

        //3. Проверим, что новый токен пользователя активен и принадлежит пользователю
        $this->assertTrue($accessToken->isActual());
        $this->assertEquals($user->getId(), $accessToken->getUser()->getId());
        $this->assertEquals($currentDevice->getId(), $accessToken->getDevice()->getId());
    }
}
