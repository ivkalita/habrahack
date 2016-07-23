<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 08.03.16
 * Time: 12:04.
 */
namespace AppBundle\Exceptions;

class PermissionDeniedException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'PermissionDenied';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 403;
    }
}
