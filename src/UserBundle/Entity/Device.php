<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 13:18.
 */
namespace UserBundle\Entity;

use AppBundle\Entity\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Device.
 *
 * @ORM\Table(name="users__devices")
 * @ORM\Entity()
 */
class Device extends TimestampableEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Unique device hardware identifier.
     *
     * @var string
     *
     * @ORM\Column(name="device_id", type="string", unique=true)
     * @Assert\NotBlank(message="Device id must not be null")
     */
    protected $deviceId;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="PlatformType", nullable=false)
     * @DoctrineAssert\Enum(entity="UserBundle\DBAL\Types\PlatformType")
     */
    protected $platform;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     *
     * @return $this
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    public function __toString()
    {
        return $this->deviceId;
    }
}
