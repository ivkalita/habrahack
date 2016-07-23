<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 15.02.16
 * Time: 8:51.
 */
namespace UserBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Model\Partial\PhonePartial;
use UserBundle\Model\Partial\PhonePartialInterface;
use UserBundle\Model\Partial\PlatformPartial;
use UserBundle\Model\Partial\PlatformPartialInterface;

class Confirmation implements PhonePartialInterface, PlatformPartialInterface
{
    use PhonePartial;
    use PlatformPartial;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="IsEmpty")
     */
    protected $password;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="IsEmpty")
     */
    protected $deviceId;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param string $deviceId
     *
     * @return $this
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }
}
