<?php


namespace App\Service;


use App\Entity\User;
use App\Form\Type\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class UserCRUDHelper
{

    private $entityManager;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory){

        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }


//    public function serialize(User $user)
//    {
//        return [
//            "email" => $user->getEmail(),
//            "name" => $user->getName(),
//            "password" => $user->getPassword()
//        ];
//    }

    /**
     * @param Request $request
     * @param FormInterface $form
     */
    public function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
    }

    /**
     * @param $id
     * @return User|object|null
     */
    public function findUser($id)
    {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $id]);

        return $user;
    }

    /**
     * @param $request
     * @param $passwordEncoder
     * @return User
     */
    public function createUser($request, $passwordEncoder)
    {
        $user = new User();
        $form = $this->formFactory->create(RegisterType::class, $user);
        $this->processForm($request, $form);
        $user->setPassword($passwordEncoder->encodePassword(
            $user,
            $form['plainPassword']->getData()
        ));

        return $user;
    }

    /**
     * @param $id
     * @param $request
     * @return User|object|null
     */
    public function updateUser($id, $request)
    {
        $user = $this->findUser($id);
        $form = $this->formFactory->create(RegisterType::class, $user);
        $this->processForm($request, $form);

        return $user;
    }
}