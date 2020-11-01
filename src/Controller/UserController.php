<?php


namespace App\Controller;


use App\Entity\ApiToken;
use App\Entity\User;
use App\Service\CreateUser;
use App\Service\FetchUser;
use App\Service\UpdateUser;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{

    private $entityManager;
    private $passwordEncoder;
    private $fetchUser;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, FetchUser $fetchUser)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->fetchUser = $fetchUser;
    }

    /**
     * @param Request $request
     * @param CreateUser $createUser
     * @return JsonResponse
     * @Route("/users", methods={"POST"})
     */
    public function createUserAction(Request $request, CreateUser $createUser)
    {
        $user = $createUser->createUser($request, $this->passwordEncoder);
        $apiToken = new ApiToken($user);

        $this->entityManager->persist($apiToken);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, 200, [], [
            'groups' => 'main'
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/users", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function displayAllUsersAction()
    {
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findAll();

        return $this->json($users, 200, [], [
            'groups' => 'main'
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/users/{id}", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function displayOneUserAction($id)
    {
        return $this->json($this->fetchUser->findUser($id), 200, [], [
            'groups' => 'main'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param UpdateUser $updateUser
     * @return JsonResponse
     * @Route("/users/{id}", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function updateUserAction($id, Request $request, UpdateUser $updateUser)
    {
        $user = $updateUser->updateUser($id, $request);

        if ($this->getUser() === $user){
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json($user, 200, [], [
                'groups' => 'main'
            ]);
        }

        return new JsonResponse([
            'message' => 'access denied'
        ], 401);
    }

    /**
     * @param $id
     * @return JsonResponse|Response
     * @Route("/users/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteUserAction($id)
    {
        $user = $this->fetchUser->findUser($id);
        if ($this->getUser() === $user){
            if ($user) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();
            }

            return new Response(null);
        }

        return new JsonResponse([
            'message' => 'access denied'
        ], 401);
    }

}