<?php

namespace App\ActivityPub;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityUtils
{

	/*
		Function to know if a GET request asks for a json-ld response.

		Returns either null, or:
		- 'application/activity+json'
		- 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"'

		Which one should be sent back is a mess. So this is the rule we apply:

		If ld+json; profile=... is requested, ld+json; profile=... is returned
		If ld+json (no profile) is requested, ld+json; profile=... is returned
		If activity+json is requested, activity+json is returned
		If both are requested (wtf mastodon), activity+json is returned
	*/
	public static function json_content_type_get(Request $request): null | string
	{
		if (!($request->isMethod('GET') || $request->isMethod('HEAD'))) {
			return null;
		}

		$accept = $request->headers->get('Accept');

		if ($accept === null) {
			return null;
		}

		$accept_list = array_map('trim', explode(',', $accept));

		if (self::is_activity_json($accept_list)) {
			return 'application/activity+json';
		}

		if (self::is_ld_json($accept_list)) {
			return 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"';
		}

		return null;
	}

	public static function is_json_content_type(string $str): bool
	{
		$list = array_map('trim', explode(',', $str));
		return self::is_activity_json($list) || self::is_ld_json($list);
	}

	private static function is_activity_json(array $list): bool
	{
		return in_array('application/activity+json', $list);
	}

	private static function is_ld_json(array $list): bool
	{
		foreach ($list as $content) {
			if (str_starts_with($content, 'application/ld+json')) {
				return true;
			}
		}

		return false;
	}
}
