<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 02.03.16
 * Time: 11:57.
 */
namespace AppBundle\Tests\Utils;

use AppBundle\Entity\City;

class CityChecker
{
    /**
     * @param $object
     * @param City $expected
     *
     * @return bool
     */
    public static function checkCard($object, $expected = null)
    {
        $result = true;

        if ($object === null) {
            return $expected === null;
        }

        $result &= array_key_exists('id', $object)
            && array_key_exists('name', $object)
            && array_key_exists('lat', $object)
            && array_key_exists('lon', $object);

        if ($expected) {
            $result &= $object['id'] === $expected->getId()
                && $object['name'] === $expected->getName()
                && $object['lat'] === $expected->getLat()
                && $object['lon'] === $expected->getLon();
        }

        return $result == 1;
    }
}
