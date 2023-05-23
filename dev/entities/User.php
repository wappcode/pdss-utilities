<?php

namespace AppEntities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
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


    protected $name;



    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Get the value of created
     *
     * @API\Field(type="DateTime")
     * @return  DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * Get the value of updated
     * @API\Field(type="DateTime")
     * @return  DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * Set the value of updated
     *
     * @API\Exclude
     * 
     *
     * @return  self
     */
    public function setUpdated()
    {
        $this->updated = new DateTime();

        return $this;
    }
}
