<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 07.04.16
 * Time: 19:16.
 */
namespace UserBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class GenderType extends AbstractEnumType
{
    const MALE = 'male';
    const FEMALE = 'female';

    protected static $choices = [
        self::MALE => 'Мужской',
        self::FEMALE => 'Женский',
    ];
}
