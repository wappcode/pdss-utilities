<?php

declare(strict_types=1);

namespace PDSSUtilities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Base class for Doctrine entities using auto-generated integer ID.
 * 
 * Provides automatic timestamp management:
 * - created: Set once when entity is first persisted
 * - updated: Updated automatically on each modification
 * 
 * @property-read int|null $id Auto-generated integer identifier
 * @property-read DateTimeImmutable $created Creation timestamp
 * @property-read DateTimeImmutable $updated Last update timestamp
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntityModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(name: 'created', type: 'datetimetz_immutable')]
    private DateTimeImmutable $created;

    #[ORM\Column(name: 'updated', type: 'datetimetz_immutable')]
    private DateTimeImmutable $updated;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->created = $now;
        $this->updated = $now;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (!isset($this->created)) {
            $this->created = new DateTimeImmutable();
        }
        if (!isset($this->updated)) {
            $this->updated = new DateTimeImmutable();
        }
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id !== null ? (string)$this->id : 'new-entity';
    }

    /**
     * Get the value of created.
     *
     * @API\Field(type="DateTime")
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * Get the value of updated.
     *
     * @API\Field(type="DateTime")
     */
    public function getUpdated(): DateTimeImmutable
    {
        return $this->updated;
    }

    /**
     * Set the value of updated.
     * Useful for manual updates outside of lifecycle callbacks.
     *
     * @API\Exclude
     */
    protected function setUpdated(): self
    {
        $this->updated = new DateTimeImmutable();
        return $this;
    }
}
