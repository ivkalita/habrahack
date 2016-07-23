<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 13:23.
 */
namespace UserBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class PlatformType.
 */
class PlatformType extends AbstractEnumType
{
    const ANDROID = 'android';
    const IOS = 'ios';

    protected static $choices = [
        self::ANDROID => 'Android',
        self::IOS => 'iOS',
    ];
}
