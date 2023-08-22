<?php

namespace App\Controller;

use App\Service\CacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends AbstractController
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    #[Route('/default-cache')]
    public function getDefault(): JsonResponse
    {
        return new JsonResponse(
            [
                'default_cache_value' => $this->cacheService->getDefaultCachedValue('default_cache_key')
            ]
        );
    }

    #[Route('/probablistic-cache')]
    public function getProbablistic(): JsonResponse
    {
        return new JsonResponse(
            [
                'probablistic_cache_value' => $this->cacheService->getProbabilisticCached('probablistic_cache_key'),
            ]
        );
    }
}