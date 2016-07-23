<?php

namespace UserBundle\Tests\Controller\API;

use AppBundle\Entity\City;
use UserBundle\Entity\AccessToken;
use UserBundle\Entity\User;
use UserBundle\Tests\Utils\UserChecker;

class UserControllerTest extends UserBundleTestCase
{
    public function testGetUserInfo()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $user = $this->fixtures['user__common'];

        $response = $this->request('/api/mobile/v1/users/me', 'GET', [], [], $token->getToken());

        $this->assertJsonResponse($response, 200);
        $data = $this->extractJsonData($response);

        $this->assertTrue(UserChecker::checkSelfCard($data, $user));
    }

    public function testUpdateUserInfoWithValidCityId()
    {
        /**
         * @var AccessToken $token
         * @var City        $city
         * @var User        $user
         */
        $token = $this->fixtures['access_token__common'];
        $city = $this->fixtures['city__vl'];
        $user = $this->fixtures['user__common'];
        $user->setCity($city)
            ->setFirstName(null)
            ->setMiddleName(null)
            ->setLastName(null);

        $response = $this->putJSONForm('/api/mobile/v1/users/me', ['city_id' => $city->getId()], $token->getToken());

        $this->assertJsonResponse($response, 200);
        $data = $this->extractJsonData($response);

        $this->assertTrue(UserChecker::checkSelfCard($data, $user));
    }

    public function testUpdateUserInfoWithInvalidCityId()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];

        $response = $this->putJSONForm('/api/mobile/v1/users/me', ['city_id' => 'error'], $token->getToken());

        $this->assertJsonResponse($response, 400);
        $this->assertErrorCode($response, 'FormInvalid');
        $this->assertHasErrorsKey($response, 'city_id', 'FormatInvalid');
    }

    public function testUpdateUserInfoWithName()
    {
        /**
         * @var AccessToken $token
         * @var User        $user
         */
        $token = $this->fixtures['access_token__common'];
        $user = $this->fixtures['user__common'];
        $newName = $user->getFirstName() . 'Ivan';
        $user->setFirstName($newName)
            ->setMiddleName(null)
            ->setLastName(null);

        $response = $this->putJSONForm('/api/mobile/v1/users/me', ['first_name' => $newName], $token->getToken());

        $this->assertJsonResponse($response, 200);
        $data = $this->extractJsonData($response);

        $this->assertTrue(UserChecker::checkSelfCard($data, $user));
    }

    public function testChipLock()
    {
        /**
         * @var AccessToken $token
         * @var User        $user
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/users/me/chip/lock', [], $token->getToken());
        $this->assertJsonResponse($response, 200);
    }

    public function testPostFeedbackSuccess()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/users/me/feedback', ['body' => 'some-text'], $token->getToken());
        $this->assertJsonResponse($response);
        $this->assertAccessTokenNotInvalid($response);
    }

    public function testPostPlayerActionAnonymous()
    {
        $response = $this->postJSONForm('/api/mobile/v1/users/me/players', ['player_id' => 'some-player-id']);
        $this->assertAccessTokenInvalid($response);
    }

    public function testPostPlayerActionSuccess()
    {
        /**
         * @var AccessToken $token
         */
        $token = $this->fixtures['access_token__common'];
        $response = $this->postJSONForm('/api/mobile/v1/users/me/players', ['player_id' => 'some-player-id'], $token->getToken());
        $this->assertJsonResponse($response, 200);
        $this->assertAccessTokenNotInvalid($response);
        $this->assertErrorCode($response, 'Success');
    }
}
