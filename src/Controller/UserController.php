<?php


namespace App\Controller;


use App\Entity\ApiToken;
use App\Entity\User;
use App\Service\UserCRUDHelper;
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
    private $CRUDHelper;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserCRUDHelper $CRUDHelper, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->CRUDHelper = $CRUDHelper;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/users", methods={"POST"})
     */
    public function createUserAction(Request $request)
    {
        $user = $this->CRUDHelper->createUser($request, $this->passwordEncoder);
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
        return $this->json($this->CRUDHelper->findUser($id), 200, [], [
            'groups' => 'main'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/users/{id}", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function updateUserAction($id, Request $request)
    {
        $user = $this->CRUDHelper->updateUser($id, $request);

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
        $user = $this->CRUDHelper->findUser($id);
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

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("api/login")
     */
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->CRUDHelper->findUserByEmail($data, $this->entityManager);

        if ($user) {
            return $this->CRUDHelper->createTokenForUser($user, $data, $this->passwordEncoder);
        }
        return new JsonResponse([
            'message' => 'invalid credentials'
        ], 401);
    }

}