<?php


namespace App\Service;


use App\Entity\User;
use App\Form\Type\RegisterType;
use Symfony\Component\Form\FormFactoryInterface;

class UpdateUser
{

    private $fetchUser;
    private $formFactory;
    private $createUser;

    public function __construct(FetchUser $fetchUser, FormFactoryInterface $formFactory, CreateUser $createUser)
    {

        $this->fetchUser = $fetchUser;
        $this->formFactory = $formFactory;
        $this->createUser = $createUser;
    }

    /**
     * @param $id
     * @param $request
     * @return User|object|null
     */
    public function updateUser($id, $request)
    {
        $user = $this->fetchUser->findUser($id);
        $form = $this->formFactory->create(RegisterType::class, $user);
        $this->createUser->processForm($request, $form);

        return $user;
    }
}