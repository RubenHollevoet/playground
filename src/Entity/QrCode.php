<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\Timestampable\Traits\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QrCodeRepository")
 */
class QrCode
{
    use Timestampable;
    use SoftDeleteable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="qrCodes")
     */
    private $code;

    public function __construct()
    {
        $this->code = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Task[]
     */
    public function getCode(): Collection
    {
        return $this->code;
    }

    public function addCode(Task $code): self
    {
        if (!$this->code->contains($code)) {
            $this->code[] = $code;
        }

        return $this;
    }

    public function removeCode(Task $code): self
    {
        if ($this->code->contains($code)) {
            $this->code->removeElement($code);
        }

        return $this;
    }
}
