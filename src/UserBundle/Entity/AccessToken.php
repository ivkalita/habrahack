<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 13:27.
 */
namespace UserBundle\Entity;

use AppBundle\Entity\TemporaryTimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AccessToken.
 *
 * @ORM\Table(name="users__access_tokens")
 * @ORM\Entity()
 */
class AccessToken extends TemporaryTimestampableEntity
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
     * @var string
     *
     * @ORM\Column(name="token", type="string")
     */
    protected $token;

    /**
     * @var Device
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Device")
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id")
     */
    protected $device;

    /**
     * OneSignal player id.
     *
     * @var string
     *
     * @ORM\Column(name="player_id", type="string", length=400, nullable=true)
     */
    protected $playerId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="accessTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @return $this
     */
    public function generateToken()
    {
        $this->setToken(uniqid(uniqid(null, true), true));

        return $this;
    }

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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param $device
     *
     * @return $this
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param string $playerId
     *
     * @return $this
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }
}
