<?php

namespace AppEntities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User
{


    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected int $id;
    #[ORM\Column(type: "string", nullable: false, length: 255, name: "name")]
    protected string $name;
    #[ORM\Column(type: "string", nullable: true, length: 255, name: "role")]
    protected ?string $role;
    #[ORM\Column(type: "datetime")]
    protected DateTime $birthday;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole(?string $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set the value of birthday
     *
     * @return  self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }
}
