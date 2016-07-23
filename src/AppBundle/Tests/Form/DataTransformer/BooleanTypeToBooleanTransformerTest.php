<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 30.03.16
 * Time: 15:42.
 */
namespace AppBundle\Tests\Form\DataTransformer;

use AppBundle\Form\DataTransformer\BooleanTypeToBooleanTransformer;
use AppBundle\Form\Type\BooleanType;

class BooleanTypeToBooleanTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $transformer = new BooleanTypeToBooleanTransformer();
        foreach ($this->getTransformData() as $pair) {
            $this->assertEquals($pair[0], $transformer->transform($pair[1]));
        }
    }

    /**
     * @return array
     */
    public function getTransformData()
    {
        return [
            [true, BooleanType::VALUE_TRUE],
            [false, BooleanType::VALUE_FALSE],
            ['1', BooleanType::VALUE_TRUE],
            ['0', BooleanType::VALUE_FALSE],
            [1, BooleanType::VALUE_TRUE],
            [0, BooleanType::VALUE_FALSE],
        ];
    }

    public function testReverseTransform()
    {
        $transformer = new BooleanTypeToBooleanTransformer();
        foreach ($this->getReverseTransformData() as $pair) {
            $this->assertEquals($pair[1], $transformer->reverseTransform($pair[0]));
        }
    }

    /**
     * @return array
     */
    public function getReverseTransformData()
    {
        return [
            [BooleanType::VALUE_TRUE, true],
            [1, true],
            ['1', true],
            [true, true],
            [BooleanType::VALUE_FALSE, false],
            [0, false],
            ['0', false],
            [false, false],
        ];
    }
}
