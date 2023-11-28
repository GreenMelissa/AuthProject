<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSession;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Сервис для работы с пользователями
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Создание пользователя
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setCity($data['city']);
        $user->setCountry($data['country']);
        $userSession = new UserSession();
        $userSession->setUser($user);
        $userSession->setAccessToken($data['access_token']);
        $this->entityManager->persist($user);
        $this->entityManager->persist($userSession);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * Обновление данных пользователя
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function updateUserData(User $user, array $data): void
    {
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setCity($data['city']);
        $user->setCountry($data['country']);
        $user->getSession()?->setAccessToken($data['access_token']);
        $this->entityManager->flush();
    }
}