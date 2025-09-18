<?php

namespace App\Controller;

use App\Security\EmailVerificationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

final class EmailVerificationController
{
    public function __construct(private readonly EmailVerificationService $emailVerificationService)
    {
    }

    #[Route('/v1/api/email/verify', name: 'v1_api_email_verify', methods: ['POST'])]
    public function __invoke(Request $request, #[MapQueryParameter] ?string $token = null): JsonResponse
    {
        $payload = $request->toArray();
        $token = $token ?? ($payload['token'] ?? null);

        if (null === $token || '' === trim($token)) {
            return new JsonResponse(['message' => 'Token manquant.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->emailVerificationService->confirmToken($token);

        if (null === $user) {
            return new JsonResponse(['message' => 'Token invalide ou expiré.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'Adresse email vérifiée avec succès.']);
    }
}
