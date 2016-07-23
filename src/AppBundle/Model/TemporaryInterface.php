<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 12:36.
 */
namespace AppBundle\Model;

/**
 * Interface TemporaryInterface.
 */
interface TemporaryInterface
{
    /**
     * Set start of activity period.
     *
     * @param \DateTime $since
     *
     * @return TemporaryInterface
     */
    public function setSince(\DateTime $since);

    /**
     * Set end of activity period.
     *
     * @param \DateTime|null $until
     *
     * @return TemporaryInterface
     */
    public function setUntil($until);

    /**
     * Force set activity.
     *
     * @param bool|null $isActive
     *
     * @return TemporaryInterface
     */
    public function setIsActive($isActive);

    /**
     * Return current entity activity status.
     *
     * @return bool
     */
    public function isActual();

    /**
     * Deactivate entity
     * Normal way for deactivating some temporary entity.
     *
     * @return mixed
     */
    public function deactivate();
}
