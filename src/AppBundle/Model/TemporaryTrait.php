<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 12:40.
 */
namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait TemporaryTrait.
 */
trait TemporaryTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="since", type="datetime")
     */
    protected $since;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="until", type="datetime", nullable=true)
     */
    protected $until;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    protected $isActive;

    /**
     * @return \DateTime
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @param \DateTime $since
     *
     * @return $this
     */
    public function setSince(\DateTime $since)
    {
        $this->since = $since;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param \DateTime $until
     *
     * @return $this
     */
    public function setUntil($until)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool|null $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function temporaryPrePersist()
    {
        if (is_null($this->since)) {
            $this->setSince(new \DateTime(null, new \DateTimeZone('UTC')));
        }
    }

    /**
     * @return bool
     */
    public function isActual()
    {
        $dt = new \DateTime(null, new \DateTimeZone('UTC'));

        return $this->until == null || $this->until->getTimestamp() > $dt->getTimestamp();
    }

    /**
     * @return $this
     */
    public function deactivate()
    {
        $this->setIsActive(null)
            ->setUntil(new \DateTime(null, new \DateTimeZone('UTC')));

        return $this;
    }
}
