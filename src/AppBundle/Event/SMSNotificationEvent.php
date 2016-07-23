<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 02.06.16
 * Time: 16:46.
 */
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use UserBundle\Entity\User;

class SMSNotificationEvent extends Event
{
    const NAME = 'app.event.sms_notification';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var User|null
     */
    protected $user;

    /**
     * SMSNotificationEvent constructor.
     *
     * @param User|null $user
     * @param string    $message
     * @param string    $phone
     */
    public function __construct(User $user = null, $message, $phone)
    {
        $this->user = $user;
        $this->message = $message;
        $this->phone = $phone;
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
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return string
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function constructMessage()
    {
        return $this->message;
    }
}
