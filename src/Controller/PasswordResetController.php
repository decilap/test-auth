<?php

namespace App\Controller;

use App\Security\PasswordResetManager;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PasswordResetController
{
    public function __construct(
        private readonly PasswordResetManager $manager,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/v1/api/password/reset/request', name: 'v1_api_password_reset_request', methods: ['POST'])]
    public function request(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $email = $payload['email'] ?? null;

        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(message: 'Adresse email requise.'),
            new Assert\Email(message: 'Adresse email invalide.'),
        ]);

        if (\count($violations) > 0) {
            return new JsonResponse([
                'message' => $violations[0]->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $this->manager->request((string) $email, $request->getClientIp() ?? 'unknown');
        } catch (TooManyRequestsHttpException $exception) {
            return new JsonResponse([
                'message' => 'Trop de tentatives. Merci de réessayer plus tard.',
            ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $exception->getHeaders());
        }

        return new JsonResponse([
            'message' => 'Si un compte existe, un email de réinitialisation a été envoyé.'
        ]);
    }

    #[Route('/v1/api/password/reset/confirm', name: 'v1_api_password_reset_confirm', methods: ['POST'])]
    public function confirm(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $token = $payload['token'] ?? null;
        $password = $payload['password'] ?? null;

        if (!$token || !\is_string($token) || trim($token) === '') {
            return new JsonResponse([
                'message' => 'Lien de réinitialisation invalide ou expiré. Demande un nouveau mail.'
            ], JsonResponse::HTTP_OK);
        }

        try {
            $success = $this->manager->reset($token, (string) $password, $request->getClientIp() ?? 'unknown');
        } catch (TooManyRequestsHttpException $exception) {
            return new JsonResponse([
                'message' => 'Trop de tentatives. Merci de réessayer plus tard.',
            ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $exception->getHeaders());
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$success) {
            return new JsonResponse([
                'message' => 'Lien de réinitialisation invalide ou expiré. Demande un nouveau mail.'
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'message' => 'Ton mot de passe a été mis à jour. Tu peux te connecter avec le nouveau mot de passe.'
        ]);
    }
}
