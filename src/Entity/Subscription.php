<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\Timestampable\Traits\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 */
class Subscription
{
    use Timestampable;
    use SoftDeleteable;

    public const STATUS_PENDING = 0;
    public const STATUS_PRE_APPROVED = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_DISAPPROVED = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function __toString()
    {
        return 'todo';
    }
}
