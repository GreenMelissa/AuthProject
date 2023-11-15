<?php

namespace App\Service;

use App\Exception\AuthException;
use App\Repository\UserRepository;

/**
 * Сервис авторизации и регистрации
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
class AuthService
{
    public function __construct(
        private readonly string $authKey,
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
    ) {}

    /**
     * Данные для регистрации и авторизации
     *
     * @var array
     */
    private array $credentials;

    /**
     * Ключ для проверки авторизации
     *
     * @var string
     */
    private string $sig;

    /**
     * Регистрация и авторизация пользователя
     *
     * @param array $data
     * @return array
     * @throws AuthException
     */
    public function authUser(array $data)
    {
        $this->prepareData($data);
        if ($this->checkCredentials()) {
            $user = $this->userRepository->find($this->credentials['id']);
            if ($user) {
                $this->userService->updateUserData($user, $this->credentials);
            } else {
                $user = $this->userService->createUser($this->credentials);
            }
            return [
                'access_token' => $user->getSession()->getAccessToken(),
                'user_info' => [
                    'id' => $user->getId(),
                    'first_name' => $user->getFirstName(),
                    'city' => $user->getCity(),
                    'country' => $user->getCountry(),
                ],
                'error' => '',
                'error_key' => '',
            ];
        }

        throw new AuthException();
    }

    /**
     * Подготовка данных
     *
     * @param array $data
     * @return void
     * @throws AuthException
     */
    private function prepareData(array $data)
    {
        if (isset($data['sig'])) {
            $this->setSig($data['sig']);
            unset($data['sig']);
        } else {
            throw new AuthException();
        }
        ksort($data);
        $this->setCredentials($data);
    }

    /**
     * Проверка подписи
     *
     * @return bool
     */
    private function checkCredentials(): bool
    {
        return mb_strtolower(md5($this->getCredentialsString()), 'UTF-8') === $this->sig;
    }

    /**
     * Получение строки для проверки по реквизитам входа
     *
     * @return string
     */
    private function getCredentialsString(): string
    {
        return implode('', array_map(
            function ($value, $key) { return sprintf('%s=%s', $key, $value); },
            $this->credentials,
            array_keys($this->credentials)
        )) . $this->authKey;
    }

    public function getCredentials(): array
    {
        return $this->credentials;
    }

    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }

    public function getSig(): string
    {
        return $this->sig;
    }

    public function setSig(string $sig): void
    {
        $this->sig = $sig;
    }
}