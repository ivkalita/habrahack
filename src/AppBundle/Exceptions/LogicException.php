<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 28.03.16
 * Time: 11:17.
 */
namespace AppBundle\Exceptions;

class LogicException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'LogicException';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 400;
    }
}
