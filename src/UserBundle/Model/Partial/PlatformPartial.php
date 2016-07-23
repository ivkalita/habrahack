<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 15.02.16
 * Time: 8:53.
 */
namespace UserBundle\Model\Partial;

use Symfony\Component\Validator\Constraints as Assert;

trait PlatformPartial
{
    /**
     * @Assert\Choice(
     *     callback={"UserBundle\DBAL\Types\PlatformType", "getValues"},
     *     message="IsUnknown"
     * )
     *
     * @Assert\NotNull(message="IsEmpty")
     *
     * @var string|null
     */
    protected $platform = null;

    /**
     * @return null|string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param $platform
     *
     * @return $this
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }
}
