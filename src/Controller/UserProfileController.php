<?php

namespace App\Controller;

use App\ActivityPub\ActivityUtils;
use App\ActivityPub\Serve\Actor;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\type;

class UserProfileController extends AbstractController
{
	private $repository;

	public function __construct(UserRepository $userRepository)
	{
		$this->repository = $userRepository;
	}

	#[Route(
		'/apub/users/{username}',
		name: 'ActivityPub User Actor',
		methods: ['GET', 'HEAD']
	)]
	public function user_ap(Request $request, $username): JsonResponse
	{
		$response_content_type = ActivityUtils::json_content_type_get($request) ?? 'application/activity+json';
		$response = new JsonResponse();
		$response->headers->set('Content-Type', $response_content_type);

		$user = $this->repository->findOneBy(array(
			'instance' => null,
			'name' => strval($username),
		));

		if ($user === null) {
			throw new NotFoundHttpException('User not found');
		}

		$activitypub_actor = new Actor($user);

		$response->setJson(json_encode($activitypub_actor));

		return $response;
	}

	#[Route(
		'/api/users/create/{username}',
		name: 'API user create (temporary)',
		methods: ['POST']
	)]
	public function user_create(EntityManagerInterface $entityManager, $username): Response
	{
		$existing_user = $this->repository->findOneBy(array(
			'instance' => null,
			'name' => strval($username),
		));

		if ($existing_user !== null) {
			throw new ConflictHttpException('User already exists');
		}

		$user = new User();

		$sslconfig = array(
			"digest_alg" => "sha512",
			"private_key_bits" => 4096,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		);

		$res = openssl_pkey_new($sslconfig);

		if ($res === false) {
			throw new Exception("Could not generate RSA keypair");
		}

		$public_key_pem = openssl_pkey_get_details($res)['key'];
		if ($public_key_pem === false) {
			throw new Exception("Could not get RSA keypair details");
		}

		$private_key_pem = "";
		$status = openssl_pkey_export($res, $private_key_pem);
		if ($status === false) {
			throw new Exception("Could not get RSA private key pem");
		}

		$user->setInstance(null);
		$user->setAvatarLink(null);
		$user->setUuid(Uuid::uuid4()->__toString());
		$user->setName(strval($username));
		$user->setDescription("<p>Hello! I'm " . strval($username) . '!</p>');
		$user->setRsaPublicKey($public_key_pem);
		$user->setRsaPrivateKey($private_key_pem);

		$entityManager->persist($user);
		$entityManager->flush();

		$response = new Response();

		$response->setStatusCode(201, 'Created');
		return $response;
	}
}
