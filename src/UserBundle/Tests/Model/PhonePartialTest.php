<?php

use AppBundle\Tests\BaseServiceTestCase;
use UserBundle\Model\Phone;

class PhonePartialTest extends BaseServiceTestCase
{
    public function testPhoneStartingWithNotNumbers()
    {
        $phonePartial = new Phone();
        $phonePartial->setPhone('abcd+79000000000');

        $validator = $this->getContainer()->get('validator');

        $this->assertEquals(1, $validator->validate($phonePartial)->count());
    }
}
