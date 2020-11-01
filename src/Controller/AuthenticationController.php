<?php


namespace App\Controller;


use App\Service\CreateToken;
use App\Service\FetchUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthenticationController extends AbstractController
{
    /**
     * @param Request $request
     * @param CreateToken $createToken
     * @param FetchUser $fetchUser
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     * @Route("api/login")
     */
    public function login(Request $request, CreateToken $createToken, FetchUser $fetchUser, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent(), true);
        $user = $fetchUser->findUserByEmail($data);

        if ($user) {
            return $createToken->createTokenForUser($user, $data, $passwordEncoder, $entityManager);
        }
        return new JsonResponse([
            'message' => 'invalid credentials'
        ], 401);
    }
}