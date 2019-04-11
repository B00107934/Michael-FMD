<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BaconRepository")
 */
class Bacon
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bacontype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBacontype(): ?string
    {
        return $this->bacontype;
    }

    public function setBacontype(string $bacontype): self
    {
        $this->bacontype = $bacontype;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
