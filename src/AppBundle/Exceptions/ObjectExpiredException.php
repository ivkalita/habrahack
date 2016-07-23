<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 28.03.16
 * Time: 16:39.
 */
namespace AppBundle\Exceptions;

class ObjectExpiredException extends LogicException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'ObjectExpired';
    }
}
