<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 17.02.16
 * Time: 11:15.
 */
namespace AppBundle\Exceptions;

class AccessTokenInvalidException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'AccessTokenInvalid';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 403;
    }
}
