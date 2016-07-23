<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 16:24.
 */
namespace UserBundle\Exceptions\Auth;

use AppBundle\Exceptions\BaseException;

class RequestRequiredException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'RequestRequired';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 403;
    }
}
