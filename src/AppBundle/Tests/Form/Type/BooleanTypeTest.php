<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 30.03.16
 * Time: 15:37.
 */
namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\BooleanType;
use Symfony\Component\Form\Test\TypeTestCase;

class BooleanTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        foreach ($this->getTestData() as $pair) {
            $type = new BooleanType();
            $form = $this->factory->create($type);

            $form->submit($pair[0]);

            $this->assertTrue($form->isSynchronized());
            $this->assertEquals($pair[1], $form->getData());
        }
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            ['1', true],
            [1, true],
            [true, true],
            ['0', false],
            [0, false],
            [false, false],
        ];
    }
}
