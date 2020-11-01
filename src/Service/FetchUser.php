<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FetchUser
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param $data
     * @return User|object|null
     */
    public function findUserByEmail($data)
    {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $data['email']]);

        return $user;
    }
}