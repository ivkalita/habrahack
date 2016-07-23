<?php

namespace AppBundle\Model;

interface TimestampableInterface
{
    /**
     * Set created at.
     *
     * @param \DateTime $createdAt
     *
     * @return TimestampableInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updated at.
     *
     * @param \DateTime $updatedAt
     *
     * @return TimestampableInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updated at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
