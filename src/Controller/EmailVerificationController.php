<?php

namespace App\Controller;

use App\Security\EmailVerificationManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EmailVerificationController
{
    public function __construct(
        private readonly EmailVerificationManager $manager,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/v1/api/email/verify', name: 'v1_api_email_verify', methods: ['GET', 'POST'])]
    public function verify(Request $request, #[MapQueryParameter] ?string $token = null): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $payload = $request->toArray();
            $token = $token ?? ($payload['token'] ?? null);
        } else {
            $token = $token ?? $request->query->get('token');
        }

        if (null === $token || '' === trim((string) $token)) {
            return new JsonResponse([
                'message' => 'Le lien de vérification est invalide ou expiré. Vous pouvez demander un nouveau courriel.',
            ], JsonResponse::HTTP_OK);
        }

        try {
            $verified = $this->manager->verifyToken($token, $request->getClientIp() ?? 'unknown');
        } catch (TooManyRequestsHttpException $exception) {
            return new JsonResponse([
                'message' => 'Trop de tentatives. Merci de réessayer plus tard.',
            ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $exception->getHeaders());
        }

        if (!$verified) {
            return new JsonResponse([
                'message' => 'Le lien de vérification est invalide ou expiré. Vous pouvez demander un nouveau courriel.',
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'message' => 'Adresse email confirmée. Votre compte est maintenant actif.'
        ]);
    }

    #[Route('/v1/api/email/resend', name: 'v1_api_email_resend', methods: ['POST'])]
    public function resend(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $email = $payload['email'] ?? null;

        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(message: 'Adresse email requise.'),
            new Assert\Email(message: 'Adresse email invalide.'),
        ]);

        if (count($violations) > 0) {
            return new JsonResponse([
                'message' => $violations[0]->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $this->manager->resendForEmail((string) $email, $request->getClientIp() ?? 'unknown');
        } catch (TooManyRequestsHttpException $exception) {
            return new JsonResponse([
                'message' => 'Trop de tentatives. Merci de réessayer plus tard.',
            ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $exception->getHeaders());
        }

        return new JsonResponse([
            'message' => 'Si un compte existe, un nouveau lien a été envoyé.'
        ]);
    }
}
