<?php


namespace App\Service;


use App\Entity\User;
use App\Form\Type\RegisterType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateUser
{

    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory){

        $this->formFactory = $formFactory;
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
     * @param Request $request
     * @param FormInterface $form
     */
    public function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
    }
}