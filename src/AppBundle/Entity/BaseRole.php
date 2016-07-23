<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

abstract class BaseRole extends TimestampableEntity implements RoleInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\Column(name="role", type="string")
     */
    protected $role;

    abstract public function getPrefix();

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRole()
    {
        return 'ROLE_' . $this->getPrefix() . '_' . $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
