<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 16:49.
 */
namespace UserBundle\Exceptions\Auth;

use AppBundle\Exceptions\BaseException;

class CredentialsInvalidException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'CredentialsInvalid';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 403;
    }
}
