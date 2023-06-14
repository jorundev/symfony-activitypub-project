<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements JsonSerializable
{
	#[ORM\Id]
         	#[ORM\GeneratedValue]
         	#[ORM\Column]
         	private ?int $id = null;

	#[ORM\Column(type: Types::GUID)]
         	private ?string $uuid = null;

	#[ORM\Column(length: 32)]
         	private ?string $name = null;

	#[ORM\Column(length: 255, nullable: true)]
         	private ?string $instance = null;

	#[ORM\Column(length: 255, nullable: true)]
         	private ?string $avatarLink = null;

	#[ORM\Column(length: 4096)]
         	private ?string $rsaPublicKey = null;

	#[ORM\Column(length: 8129)]
         	private ?string $rsaPrivateKey = null;

    #[ORM\Column(length: 65535)]
    private ?string $description = null;

	public function getId(): ?int
         	{
         		return $this->id;
         	}

	public function getUuid(): ?string
         	{
         		return $this->uuid;
         	}

	public function setUuid(string $uuid): static
         	{
         		$this->uuid = $uuid;
         
         		return $this;
         	}

	public function getName(): ?string
         	{
         		return $this->name;
         	}

	public function setName(string $name): static
         	{
         		$this->name = $name;
         
         		return $this;
         	}

	public function getInstance(): ?string
         	{
         		return $this->instance;
         	}

	public function setInstance(?string $instance): static
         	{
         		$this->instance = $instance;
         
         		return $this;
         	}

	public function getAvatarLink(): ?string
         	{
         		return $this->avatarLink;
         	}

	public function setAvatarLink(?string $avatarLink): static
         	{
         		$this->avatarLink = $avatarLink;
         
         		return $this;
         	}

	public function jsonSerialize(): mixed
         	{
         		return array(
         			'uuid' => $this->getUuid(),
         			'name' => $this->getName(),
         			'instance' => $this->getInstance(),
         			'avatar_link' => $this->getAvatarLink(),
         		);
         	}

	public function getRsaPublicKey(): ?string
         	{
         		return $this->rsaPublicKey;
         	}

	public function setRsaPublicKey(string $rsaPublicKey): static
         	{
         		$this->rsaPublicKey = $rsaPublicKey;
         
         		return $this;
         	}

	public function getRsaPrivateKey(): ?string
         	{
         		return $this->rsaPrivateKey;
         	}

	public function setRsaPrivateKey(string $rsaPrivateKey): static
         	{
         		$this->rsaPrivateKey = $rsaPrivateKey;
         
         		return $this;
         	}

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
