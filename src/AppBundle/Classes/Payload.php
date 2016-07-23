<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 11:59.
 */
namespace AppBundle\Classes;

class Payload
{
    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $httpCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Payload constructor.
     *
     * @param mixed  $data
     * @param string $status
     * @param int    $httpCode
     * @param array  $errors
     */
    public function __construct($data = null, $status = 'Success', $httpCode = 200, $errors = [])
    {
        if ($data === null) {
            $data = new \stdClass();
        }
        $this->data = $data;
        $this->status = $status;
        $this->httpCode = $httpCode;
        $this->message = $status;
        $this->errors = $errors;
    }

    /**
     * @param mixed  $data
     * @param string $status
     * @param int    $httpCode
     * @param array  $errors
     *
     * @return Payload
     */
    public static function create($data = null, $status = 'Success', $httpCode = 200, array $errors = [])
    {
        return new self($data, $status, $httpCode, $errors);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
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

    /**
     * @param $data
     *
     * @return array|\stdClass
     */
    protected function handleEmptyData($data)
    {
        if ($data) {
            return $data;
        }

        return is_array($data) ? [] : new \stdClass();
    }

    /**
     * @return array
     */
    public function getForResponse()
    {
        return [
            'data' => $this->handleEmptyData($this->data),
            'status' => $this->status,
            'errors' => $this->errors,
        ];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->data[$key];
    }
}
