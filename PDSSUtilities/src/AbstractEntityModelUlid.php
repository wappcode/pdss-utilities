<?php

declare(strict_types=1);

namespace PDSSUtilities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use PDSSUtilities\DoctrineUlidGenerator;

/**
 * Base class for Doctrine entities using ULID as primary key.
 * 
 * Provides automatic timestamp management:
 * - created: Set once when entity is first persisted
 * - updated: Updated automatically on each modification
 * 
 * @property-read string|null $id ULID identifier (26 characters)
 * @property-read DateTimeImmutable $created Creation timestamp
 * @property-read DateTimeImmutable $updated Last update timestamp
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntityModelUlid
{
    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: 'string',
        length: 26,
        options: ['fixed' => true]
    )]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: DoctrineUlidGenerator::class)]
    protected ?string $id = null;

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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id ?? 'new-entity';
    }

    /**
     * Get the value of created.
     */
    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    /**
     * Get the value of updated.
     */
    public function getUpdated(): DateTimeImmutable
    {
        return $this->updated;
    }

    /**
     * Set the value of updated.
     * Useful for manual updates outside of lifecycle callbacks.
     */
    protected function setUpdated(): self
    {
        $this->updated = new DateTimeImmutable();
        return $this;
    }
}
