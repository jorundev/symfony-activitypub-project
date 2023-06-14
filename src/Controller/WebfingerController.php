<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class WebfingerController extends AbstractController
{
	private $repository;

	public function __construct(UserRepository $userRepository)
	{
		$this->repository = $userRepository;
	}

	#[Route(
		'/.well-known/webfinger',
		name: "webfinger",
		condition: "request.query.get('resource') !== null",
		methods: ['GET', 'HEAD']
	)]
	public function webfinger(Request $request): JsonResponse
	{
		$subject = strval($request->query->get('resource'));

		$account = null;

		if (!str_starts_with($subject, 'acct:')) {
			$account = (string)$subject;
			$subject = 'acct:' . $account;
		} else {
			$account = substr($subject, 5);
		}

		if (!str_contains($account, '@')) {
			throw new NotFoundHttpException();
		}

		$handle = explode('@', $account, 2);

		if (count($handle) !== 2) {
			throw new NotFoundHttpException();
		}

		$name = $handle[0];
		$domain = $handle[1];

		if ($domain !== $_ENV['AP_DOMAIN']) {
			throw new UnprocessableEntityHttpException('Requested resource is on domain "' . $domain . '". Expected "' . $_ENV['AP_DOMAIN'] . '".');
		}

		$user = $this->repository->findOneBy(array(
			'instance' => null,
			'name' => $name,
		));

		if ($user === null) {
			throw new NotFoundHttpException();
		}

		$profile_link = 'https://' . $_ENV['AP_DOMAIN'] . '/apub/users/' . $name;

		return new JsonResponse(array(
			'subject' => $subject,
			'aliases' => array($profile_link),
			'links' => array(
				'rel' => 'self',
				'type' => 'application/activity+json',
				'href' => $profile_link,
			)
		));
	}
}
