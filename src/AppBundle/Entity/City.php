<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 01.03.16
 * Time: 17:37.
 */
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * Class City.
 *
 * @ORM\Entity()
 * @ORM\Table(name="app__cities")
 *
 * @JMS\ExclusionPolicy("all")
 */
class City extends TimestampableEntity
{
    const LIST_CARD = 'city__list';
    /**
     * @var int
     *
     * @JMS\Expose()
     * @JMS\Groups({"all"})
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Groups({"all"})
     *
     * @ORM\Column(name="name", type="string", length=250)
     *
     * @Assert\NotBlank(message="IsEmpty")
     */
    protected $name;

    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Groups({"all"})
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    protected $lat;

    /**
     * @var float
     *
     * @JMS\Expose()
     * @JMS\Groups({"all"})
     *
     * @ORM\Column(name="lon", type="float", nullable=true)
     */
    protected $lon;

    /**
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User", mappedBy="city")
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param float $lon
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
    }

    public function __toString()
    {
        return $this->name;
    }
}
