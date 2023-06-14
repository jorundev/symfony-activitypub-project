<?php

namespace App\ActivityPub\Serve;

use JsonSerializable;

abstract class ActivityPubEntity implements JsonSerializable
{
	private array $context;
	public function __construct()
	{
		$this->context = array('https://www.w3.org/ns/activitystreams');
	}

	protected function addToContext(string | array $obj)
	{
		if (!in_array($obj, $this->context)) {
			$this->context[] = $obj;
		}
	}

	public function getContext(): string | array
	{
		if (count($this->context) === 1) {
			return strval($this->context[0]);
		}

		return $this->context;
	}

	abstract public function getContent(): array;

	public function getDocument(): array
	{
		return array(
			'@context' => $this->getContext(),
			...$this->getContent(),
		);
	}

	public function jsonSerialize(): mixed
	{
		return $this->getDocument();
	}
}
