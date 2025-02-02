<?php

declare(strict_types=1);

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/api/me')]
    #[IsGranted('ROLE_USER')]
    public function me(): Response
    {
        return $this->json(['message' => 'Bonjour']);
    }
}
