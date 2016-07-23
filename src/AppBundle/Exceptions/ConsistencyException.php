<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 09.03.16
 * Time: 11:26.
 */
namespace AppBundle\Exceptions;

/**
 * Class ConsistencyException.
 */
class ConsistencyException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'ConsistencyException';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 500;
    }
}
