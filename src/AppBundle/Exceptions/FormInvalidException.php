<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 12:56.
 */
namespace AppBundle\Exceptions;

class FormInvalidException extends BaseException
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'FormInvalid';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 400;
    }
}
