<?php

namespace UserBundle\Tests\Controller\API;

use AppBundle\Tests\Controller\BaseControllerTestCase;

class UserBundleTestCase extends BaseControllerTestCase
{
    protected function getFixtureFiles()
    {
        return [
            '@UserBundle/DataFixtures/ORM/access_token.yml',
            '@UserBundle/DataFixtures/ORM/city.yml',
            '@UserBundle/DataFixtures/ORM/passport.yml',
            '@UserBundle/DataFixtures/ORM/user.yml',
            '@UserBundle/DataFixtures/ORM/batch.yml',
            '@UserBundle/DataFixtures/ORM/chip.yml',
            '@UserBundle/DataFixtures/ORM/phone.yml',
        ];
    }
}
