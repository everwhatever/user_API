<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\UserCRUDHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private $entityManager;
    private $CRUDHelper;

    public function __construct(EntityManagerInterface $entityManager, UserCRUDHelper $CRUDHelper)
    {
        $this->entityManager = $entityManager;
        $this->CRUDHelper = $CRUDHelper;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/user/create")
     */
    public function createUserAction(UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $user = $this->CRUDHelper->createUser($request, $passwordEncoder);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, 200, [], [
            'groups'=>'main'
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/users")
     */
    public function displayAllUsersAction()
    {
        $repository = $this->entityManager->getRepository(User::class);
        $users = $repository->findAll();

        return $this->json($users, 200, [], [
            'groups'=>'main'
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/user/{id}")
     */
    public function displayOneUserAction($id)
    {
        #$this->CRUDHelper->serialize()
        #return $this->json($this->CRUDHelper->findUser($id));
        return $this->json($this->CRUDHelper->findUser($id), 200, [], [
            'groups'=>'main'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/user/update/{id}")
     */
    public function updateUserAction($id, Request $request)
    {
        $user = $this->CRUDHelper->updateUser($id, $request);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, 200, [], [
            'groups'=>'main'
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/user/delete/{id}")
     */
    public function deleteUserAction($id)
    {
        $user = $this->CRUDHelper->findUser($id);

        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        return new Response(null);
    }


}