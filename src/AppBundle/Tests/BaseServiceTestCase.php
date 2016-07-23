<?php

namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 29.03.16
 * Time: 14:42.
 */
class BaseServiceTestCase extends WebTestCase
{
    /**
     * @var Container
     */
    protected static $container;

    /**
     * @var string
     */
    protected static $baseFixturesPath;

    /**
     * @var array
     */
    protected $fixtures;

    /**
     * @param string|null $name
     */
    protected function loadTestBasedFixture($name = null)
    {
        $paths = $name ? [static::$baseFixturesPath . '/' . $name] : [];
        $this->getContainer()->set('doctrine', static::$container->get('doctrine'));
        $this->fixtures = $this->loadFixtureFiles($paths);
    }

    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        static::$container = $kernel->getContainer();
    }
}
