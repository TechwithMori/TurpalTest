<?php

namespace App\Services\Providers;

use App\Contracts\ProviderInterface;
use App\Models\Experience;
use App\Services\V1\ExperienceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExperienceProviderService
{
    private array $providers;
    private ExperienceService $localExperienceService;

    public function __construct(ExperienceService $localExperienceService)
    {
        $this->localExperienceService = $localExperienceService;
        $this->providers = [
            new HeavenlyToursProvider(),
        ];
    }

    /**
     * Get available experiences from all sources (local + providers)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $filters
     * @return array
     */
    public function getAvailableExperiences(Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $cacheKey = "all_experiences_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate, $filters) {
            $allExperiences = [];

            // Get local experiences
            $localExperiences = $this->localExperienceService->getAvailableExperiences($startDate, $endDate);
            $allExperiences = array_merge($allExperiences, $localExperiences);

            // Get provider experiences
            foreach ($this->providers as $provider) {
                if ($provider->isAvailable()) {
                    try {
                        $providerExperiences = $provider->getAvailableExperiences($startDate, $endDate, $filters);
                        $allExperiences = array_merge($allExperiences, $providerExperiences);
                    } catch (\Exception $e) {
                        Log::error("Provider {$provider->getProviderName()} failed", [
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            }


            usort($allExperiences, function ($a, $b) {
                $aSource = $a['source'] ?? 'local';
                $bSource = $b['source'] ?? 'local';

                if ($aSource === 'local' && $bSource !== 'local') return -1;
                if ($bSource === 'local' && $aSource !== 'local') return 1;

                return 0;
            });

            return $allExperiences;
        });
    }

    /**
     * @param string $experienceId
     * @return array|null
     */
    public function getExperienceDetails(string $experienceId): ?array
    {
        // First try to find in local database
        $localExperience = Experience::find($experienceId);
        if ($localExperience) {
            return $this->localExperienceService->getExperienceDetails($localExperience);
        }

        // Try providers
        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                try {
                    $details = $provider->getExperienceDetails($experienceId);
                    if ($details) {
                        return $details;
                    }
                } catch (\Exception $e) {
                    Log::error("Provider {$provider->getProviderName()} failed to get details", [
                        'experience_id' => $experienceId,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * Get experience availability from all sources
     *
     * @param string $experienceId
     * @param Carbon $date
     * @return array
     */
    public function getExperienceAvailability(string $experienceId, Carbon $date): array
    {
        // First try local database
        $localExperience = Experience::find($experienceId);
        if ($localExperience) {
            return $this->localExperienceService->getExperienceAvailability($experienceId, $date);
        }

        // Try providers
        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                try {
                    $availability = $provider->getExperienceAvailability($experienceId, $date);
                    if (!empty($availability)) {
                        return $availability;
                    }
                } catch (\Exception $e) {
                    Log::error("Provider {$provider->getProviderName()} failed to get availability", [
                        'experience_id' => $experienceId,
                        'date' => $date->format('Y-m-d'),
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return ['available' => false, 'prices' => []];
    }

    /**
     * Get all available providers
     *
     * @return array
     */
    public function getAvailableProviders(): array
    {
        $available = ['local'];

        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                $available[] = $provider->getProviderName();
            }
        }

        return $available;
    }

    /**
     * Get experiences by source
     *
     * @param string $source
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $filters
     * @return array
     */
    public function getExperiencesBySource(string $source, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        if ($source === 'local') {
            return $this->localExperienceService->getAvailableExperiences($startDate, $endDate);
        }

        foreach ($this->providers as $provider) {
            if ($provider->getProviderName() === $source && $provider->isAvailable()) {
                return $provider->getAvailableExperiences($startDate, $endDate, $filters);
            }
        }

        return [];
    }

    /**
     * Check if an experience exists in any source
     *
     * @param string $experienceId
     * @return bool
     */
    public function experienceExists(string $experienceId): bool
    {
        // Check local database
        if (Experience::find($experienceId)) {
            return true;
        }

        // Check providers
        foreach ($this->providers as $provider) {
            if ($provider->isAvailable()) {
                try {
                    $details = $provider->getExperienceDetails($experienceId);
                    if ($details) {
                        return true;
                    }
                } catch (\Exception $e) {
                    // Continue to next provider
                }
            }
        }

        return false;
    }
}
