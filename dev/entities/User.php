<?php

namespace AppEntities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * 
     */
    protected $id;


    /**
     * @ORM\Column(type="string", nullable=false, length=255, name="name")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string",nullable=true,length=255,name="role")
     *
     * @var string
     */
    protected $role;


    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $birthday;

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of birthday
     *
     * @return  DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set the value of birthday
     *
     * @param  DateTime  $birthday
     *
     * @return  self
     */
    public function setBirthday(DateTime $birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get the value of role
     *
     * @return  string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @param  string  $role
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        $this->role = $role;

        return $this;
    }
}
