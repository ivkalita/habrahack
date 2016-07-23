<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 16:14.
 */
namespace AppBundle\Exceptions;

class NotFoundException extends BaseException
{
    /**
     * @var string
     */
    protected $what;

    /**
     * NotFoundException constructor.
     *
     * @param array  $errors
     * @param string $what
     */
    public function __construct(array $errors = [], $what = null)
    {
        parent::__construct($errors);
        $this->what = $what;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return ($this->what ? ucfirst($this->what) : '') . 'NotFound';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return 404;
    }
}
