<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 15.02.16
 * Time: 11:59.
 */
namespace UserBundle\Tests\Controller\API;

use UserBundle\Entity\AccessToken;
use UserBundle\Entity\User;

class AuthControllerTest extends UserBundleTestCase
{
    public function testAuthRequestAnonymous()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/request');
        $this->assertAccessTokenNotInvalid($response);
    }

    public function testAuthRequestWithoutPhoneAction()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/request', ['phone' => '+7000000000']);

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'phone');
    }

    public function testAuthRequestWithPhoneAction()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/request', ['phone' => '+70000000000']);

        $this->assertJsonResponse($response, 200);
        $this->assertErrorCode($response, 'Success');
        $data = $this->extractJsonData($response);
        $this->assertArrayHasKey('secret', $data);
    }

    public function testAuthConfirmationAnonymous()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/request');
        $this->assertAccessTokenNotInvalid($response);
    }

    public function testConfirmationWithoutPhone()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/confirm', [
            'password' => 'password',
            'platform' => 'ios',
            'device_id' => 'device_id',
        ]);

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'phone', 'FormatInvalid');
    }

    public function testConfirmationWithInvalidPhone()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/confirm', [
            'password' => 'password',
            'platform' => 'ios',
            'device_id' => 'device_id',
            'phone' => '89000000',
        ]);

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'phone', 'FormatInvalid');
    }

    public function testConfirmationWithoutPassword()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/confirm', [
            'phone' => '+70000000000',
            'platform' => 'ios',
            'device_id' => 'device_id',
        ]);

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'password', 'IsEmpty');
    }

    public function testConfirmationWithoutPlatform()
    {
        $response = $this->postJSONForm('/api/mobile/v1/auth/confirm', [
            'phone' => '+70000000000',
            'password' => 'text',
            'device_id' => 'device_id',
        ]);

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'platform', 'IsEmpty');
    }

    public function testConfirmation()
    {
        /**
         * @var User $user
         */
        $user = $this->fixtures['user__request'];
        $password = hash('sha256', $user->getSmsCode() . $user->getSecret());

        $response = $this->postJSONForm('/api/mobile/v1/auth/confirm', [
            'phone' => $this->fixtures['phone__request']->getPhone(),
            'password' => $password,
            'device_id' => 'device_id',
            'platform' => 'ios',
        ]);
        $this->assertJsonResponse($response, 200);
        $data = $this->extractJsonData($response);
        $this->assertArrayHasKey('is_new', $data);
        $this->assertArrayHasKey('access_token', $data);
        $this->assertTrue($data['is_new']);
    }

    public function testLogoutAnonymous()
    {
        $response = $this->request('/api/mobile/v1/logout', 'POST');
        $this->assertAccessTokenInvalid($response);
    }

    public function testLogoutAuthenticated()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->request('/api/mobile/v1/logout', 'POST', [], [], $token->getToken());
        $this->assertJsonResponse($response, 200);

        $response = $this->request('/api/mobile/v1/logout', 'POST', [], [], $token->getToken());
        $this->assertAccessTokenInvalid($response);
    }

    public function testChangePhoneRequestAnonymous()
    {
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/request', ['new_phone' => '+79000000000']);
        $this->assertAccessTokenInvalid($response);
    }

    public function testChangePhoneRequestAuthenticated()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/request', ['new_phone' => '+79000000000'], $token->getToken());
        $this->assertJsonResponse($response, 200);
        $this->assertAccessTokenNotInvalid($response);
        $data = $this->extractJsonData($response);
        $this->assertArrayHasKey('secret', $data);
        $this->assertNotNull($data['secret']);
    }

    public function testChangePhoneRequestWithoutNewPhone()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/request', [], $token->getToken());
        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'new_phone', 'FormatInvalid');
    }

    public function testConfirmChangePhoneRequestAnonymous()
    {
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/confirm');
        $this->assertAccessTokenInvalid($response);
    }

    public function testConfirmChangePhoneRequestWithoutOldPass()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/confirm', ['new_code' => 'asdf'], $token->getToken());
        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'old_code', 'IsEmpty');
    }

    public function testConfirmChangePhoneRequestWithoutNewPass()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/change-phone/confirm', ['old_code' => 'asdf'], $token->getToken());
        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'new_code', 'IsEmpty');
    }
}
