<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 15.02.16
 * Time: 12:13.
 */
namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class BaseControllerTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var
     */
    protected static $baseFixturesPath;

    /**
     * @var mixed
     */
    protected $fixtures;

    /**
     * @param null $name
     */
    protected function loadTestBasedFixture($name = null)
    {
        $paths = $name ? [static::$baseFixturesPath . '/' . $name] : [];
        $this->fixtures = $this->loadFixtureFiles($paths);
    }

    /**
     * @return User
     */
    protected function getSuperAdmin()
    {
        return $this->fixtures['user__super_admin'];
    }

    /**
     * Returns list of fixtures files for testcase.
     *
     * If you won't load all fixtures, you can override this method
     * You MUST append new fixture paths in this method
     *
     * //TODO: Use BundleResolver and AppKernel to get ALL fixture files
     *
     * @return string[]
     */
    protected function getFixtureFiles()
    {
        return [];
    }

    /**
     *
     */
    public function setUp()
    {
        $this->client = static::createClient();
        ini_set('zend.enable_gc', 0);
        $this->fixtures = $this->loadFixtureFiles($this->getFixtureFiles());
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     * @param string   $message
     */
    protected function assertJsonResponse(Response $response, $statusCode = 200, $message = '')
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent() . "\n$message");
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }

    /**
     * @param Response $response
     * @param string   $message
     */
    protected function assertAccessTokenInvalid(Response $response, $message = '')
    {
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
        $this->assertNotEquals(500, $response->getStatusCode(), 'GeneralInternalError occured!');
        $this->assertEquals(403, $response->getStatusCode(), $response->getContent() . "\n$message");
        $this->assertErrorCode($response, 'AccessTokenInvalid');
    }

    /**
     * @param Response $response
     * @param string   $message
     */
    protected function assertAccessTokenNotInvalid(Response $response, $message = '')
    {
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
        $this->assertNotEquals(500, $response->getStatusCode(), 'GeneralInternalError occured!');
        $this->assertNotEquals(403, $response->getStatusCode(), $response->getContent() . "\n$message");
    }

    protected function assertApiKeyInvalid(Response $response, $message = '')
    {
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
        $this->assertNotEquals(500, $response->getStatusCode(), 'GeneralInternalError occured!');
        $this->assertEquals(403, $response->getStatusCode(), $response->getContent() . "\n$message");
        $this->assertErrorCode($response, 'ApiKeyInvalid');
    }

    /**
     * @param Response $response
     * @param bool     $asAssocArray
     *
     * @return mixed
     */
    protected function extractJsonData(Response $response, $asAssocArray = true)
    {
        $data = json_decode($response->getContent(), $asAssocArray);

        return $asAssocArray ? $data['data'] : $data->data;
    }

    /**
     * @param Response $response
     * @param $code
     * @param string $message
     */
    protected function assertErrorCode(Response $response, $code, $message = '')
    {
        $errorCode = json_decode($response->getContent(), true)['status'];
        $this->assertTrue($code === $errorCode, "Expected error code $code, found $errorCode\n$message");
    }

    /**
     * @param $data
     *
     * @return string
     */
    protected function body($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $url
     * @param $data
     * @param string $token
     *
     * @return null|Response
     */
    protected function postJSONForm($url, $data = [], $token = '')
    {
        return $this->request($url, 'POST', [], $data, $token);
    }

    /**
     * @param string $url
     * @param $data
     * @param string $token
     *
     * @return null|Response
     */
    protected function putJSONForm($url, $data = [], $token = '')
    {
        return $this->request($url, 'PUT', [], $data, $token);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param array  $data
     * @param string $token
     *
     * @return null|Response
     */
    protected function request($url, $method = 'GET', $params = [], $data = [], $token = '')
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($token) {
            $headers['HTTP_AUTHORIZATION'] = "TOKEN token=$token";
        }
        $this->client->request($method, $url, $params, [], $headers, $this->body($data));

        return $this->client->getResponse();
    }

    /**
     * @param Response $response
     * @param $field
     * @param null $errorName
     */
    protected function assertHasErrorsKey(Response $response, $field, $errorName = null)
    {
        $errors = json_decode($response->getContent(), true)['errors'];

        $found = false;
        foreach ($errors as $error) {
            if (!array_key_exists('field', $error)) {
                $this->assertTrue(false, 'Error format invalid');
            }
            $localFound = $error['field'] == $field;
            if ($errorName !== null) {
                $localFound &= $error['error'] == $errorName;
            }

            $found |= $localFound;
        }

        if (!$found) {
            $this->assertTrue(false, 'Error for this key not found');
        }
    }

    /**
     * @param User   $user
     * @param string $password
     */
    public function adminFormLogin(User $user, $password)
    {
        $crawler = $this->client->request('GET', '/admin/login');
        $form = $crawler->selectButton('Войти')->form([
            '_username' => $user->getActivePhone()->getPhone(),
            '_password' => $password,
        ]);
        $this->client->submit($form);
    }

    public function assertPageAccessibility($url, $expectedStatusCode)
    {
        $this->client->request('GET', $url);
        $this->assertStatusCode($expectedStatusCode, $this->client);
    }
}
