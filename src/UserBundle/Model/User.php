<?php

namespace UserBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 */
class User
{
    /**
     * @Assert\Range(
     *     min=1,
     *     minMessage="FormatInvalid",
     *     invalidMessage="FormatInvalid"
     * )
     */
    protected $cityId = null;

    /**
     * @var null|string
     */
    protected $firstName = null;

    /**
     * @var null|string
     */
    protected $lastName = null;

    /**
     * @var null|string
     */
    protected $middleName = null;

    /**
     * @return int
     */
    public function getCityId()
    {
        return intval($this->cityId);
    }

    /**
     * @param $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }

    /**
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param null $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param null $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param null $middleName
     *
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }
}
