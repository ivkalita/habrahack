<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 10.05.16
 * Time: 14:53.
 */
namespace AppBundle\Tests\Utils;

class ImageChecker
{
    /**
     * @param $object
     *
     * @return bool
     */
    public static function checkGeneralCard($object)
    {
        return array_key_exists('thumbnail', $object)
            && array_key_exists('standard', $object)
            && array_key_exists('original', $object);
    }
}
