<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 19.05.16
 * Time: 13:56.
 */
namespace UserBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PlayerId
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="IsEmpty")
     */
    protected $playerId;

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
}
