<?php


namespace App\Service;


use App\Entity\ApiToken;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateToken
{
    /**
     * @param $user
     * @param $data
     * @param $passwordEncoder
     * @param $entityManager
     * @return JsonResponse
     */
    public function createTokenForUser($user, $data, $passwordEncoder, $entityManager)
    {
        if ($passwordEncoder->isPasswordValid($user, $data['password'])) {
            $apiToken = new ApiToken($user);
            $entityManager->persist($apiToken);
            $entityManager->flush();

            return new JsonResponse(['token' => $apiToken->getToken()]);
        } else {
            return new JsonResponse([
                'message' => 'invalid credentials'
            ], 401);
        }
    }
}