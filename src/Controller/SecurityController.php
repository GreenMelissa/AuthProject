<?php

namespace App\Controller;

use App\Exception\AuthException;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер авторизации и регистрации в системе
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
#[Route(path: '/', name: 'security_')]
class SecurityController extends AbstractController
{
    #[Route(path: '/user_auth', name: 'user_auth', methods: ['GET'])]
    public function actionAuth(Request $request, AuthService $authService)
    {
        try {
            return new JsonResponse($authService->authUser($request->query->all()));
        } catch (AuthException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'error_key' => AuthException::ERROR_KEY,
            ]);
        }
    }
}