<?php

namespace App\Contracts;

use Carbon\Carbon;

interface ProviderInterface
{
    /**
     * Get available experiences/tours from the provider
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $filters
     * @return array
     */
    public function getAvailableExperiences(Carbon $startDate, Carbon $endDate, array $filters = []): array;

    /**
     * Get detailed information about a specific experience/tour
     *
     * @param string $experienceId
     * @return array|null
     */
    public function getExperienceDetails(string $experienceId): ?array;

    /**
     * Get availability and pricing for a specific experience/tour
     *
     * @param string $experienceId
     * @param Carbon $date
     * @return array
     */
    public function getExperienceAvailability(string $experienceId, Carbon $date): array;

    /**
     * Get the provider name
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Check if the provider is available/healthy
     *
     * @return bool
     */
    public function isAvailable(): bool;
}
