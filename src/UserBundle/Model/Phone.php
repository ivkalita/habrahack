<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 13.02.16
 * Time: 11:50.
 */
namespace UserBundle\Model;

use UserBundle\Model\Partial\PhonePartial;
use UserBundle\Model\Partial\PhonePartialInterface;

class Phone implements PhonePartialInterface
{
    use PhonePartial;
}
