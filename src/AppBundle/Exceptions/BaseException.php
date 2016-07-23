<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 12:56.
 */
namespace AppBundle\Exceptions;

class BaseException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * BaseException constructor.
     *
     * @param array $errors
     */
    public function __construct($errors = [])
    {
        $this->errors = $errors;
        parent::__construct($this->getName(), $this->getHttpCode(), null);
    }

    public function getName()
    {
        return 'BaseException';
    }

    public function getHttpCode()
    {
        return 500;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
