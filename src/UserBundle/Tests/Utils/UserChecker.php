<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 14.03.16
 * Time: 17:01.
 */
namespace UserBundle\Tests\Utils;

use AppBundle\Tests\Utils\CityChecker;
use ChipBundle\Tests\Utils\ChipChecker;
use UserBundle\Entity\User;

class UserChecker
{
    /**
     * @param $user
     * @param null|User $expected
     *
     * @return bool
     */
    public static function checkSelfCard($user, $expected = null)
    {
        $result = true;

        $result &= array_key_exists('id', $user)
            && is_int($user['id'])
            && array_key_exists('is_confirmed', $user)
            && array_key_exists('phone', $user)
            && array_key_exists('gender', $user)
            && array_key_exists('chip', $user);

        if (array_key_exists('city', $user)) {
            $result &= CityChecker::checkCard($user['city'], $expected ? $expected->getCity() : null);
        }

        if ($user['chip'] !== null) {
            $result &= ChipChecker::checkFullCard($user['chip']);
        }

        if ($expected) {
            $result &= $expected->getId() === $user['id']
                && $user['is_confirmed'] === $expected->isConfirmed();

            if ($expected->getFirstName()) {
                $result &= array_key_exists('first_name', $user)
                    && $user['first_name'] === $expected->getFirstName();
            }
            if ($expected->getLastName()) {
                $result &= array_key_exists('last_name', $user)
                    && $user['last_name'] === $expected->getLastName();
            }
            if ($expected->getMiddleName()) {
                $result &= array_key_exists('middle_name', $user)
                    && $user['middle_name'] === $expected->getMiddleName();
            }
            if ($expected->getGender()) {
                $result &= $user['gender'] === $expected->getGender();
            }
        }

        return $result == 1;
    }

    public static function checkPublicCard($object)
    {
        $result = true;

        $result &= array_key_exists('id', $object)
            && array_key_exists('first_name', $object)
            && array_key_exists('middle_name', $object)
            && array_key_exists('last_name', $object)
            && array_key_exists('phone', $object)
            && array_key_exists('is_confirmed', $object);

        return $result == 1;
    }
}
