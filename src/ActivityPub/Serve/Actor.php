<?php

namespace App\ActivityPub\Serve;

use App\Entity\User;

class Actor extends ActivityPubEntity
{
	private string $name;
	private string $description;
	private string $rsa_public_key;

	public function __construct(User $user)
	{
		parent::__construct();
		$this->addToContext("https://w3id.org/security/v1");

		$this->name = $user->getName();
		$this->rsa_public_key = $user->getRsaPublicKey();
		$this->description = $user->getDescription();
	}

	public function getContent(): array
	{
		$user_uri = 'https://' . $_ENV['AP_DOMAIN'] . '/apub/users/' . $this->name;
		return array(
			'name' => $this->name,
			'preferredUsername' => $this->name,
			'id' => $user_uri,
			'url' => $user_uri,
			'type' => 'Person',
			'alsoKnownAs' => array(),
			'discoverable' => true,
			'inbox' => $user_uri . '/inbox',
			'outbox' => $user_uri . '/outbox',
			'publicKey' => array(
				'id' => $user_uri . '#main-key',
				'owner' => $user_uri,
				'publicKeyPem' => $this->rsa_public_key,
			),
			'summary' => $this->description,
			'tag' => array(),
		);
	}
}
