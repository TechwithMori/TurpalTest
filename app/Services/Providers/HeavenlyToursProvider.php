<?php

namespace App\Services\Providers;

use App\Contracts\ProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HeavenlyToursProvider implements ProviderInterface
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;
    private int $cacheDuration;

    public function __construct()
    {
        $this->baseUrl = config('providers.heavenly_tours.base_url', 'https://mock.turpal.com');
        $this->apiKey = config('providers.heavenly_tours.api_key', '');
        $this->timeout = config('providers.heavenly_tours.timeout', 30);
        $this->cacheDuration = config('providers.heavenly_tours.cache_duration', 3600);
    }

    public function getAvailableExperiences(Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $cacheKey = "heavenly_tours_experiences_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($startDate, $endDate, $filters) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/tours", [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'limit' => $filters['limit'] ?? 50,
                        'page' => $filters['page'] ?? 1,
                    ]);

                if (!$response->successful()) {
                    Log::error('Heavenly Tours API error', [
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                    return $this->getMockExperiences();
                }

                $data = $response->json();
                return $this->normalizeExperiences($data['data'] ?? [], $startDate, $endDate);

            } catch (\Exception $e) {
                Log::error('Heavenly Tours API exception', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return $this->getMockExperiences();
            }
        });
    }

    public function getExperienceDetails(string $experienceId): ?array
    {
        $cacheKey = "heavenly_tours_experience_details_{$experienceId}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($experienceId) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/tours/{$experienceId}");

                if (!$response->successful()) {
                    Log::error('Heavenly Tours API error - get details', [
                        'experience_id' => $experienceId,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                    return null;
                }

                $data = $response->json();
                return $this->normalizeExperienceDetails($data);

            } catch (\Exception $e) {
                Log::error('Heavenly Tours API exception - get details', [
                    'experience_id' => $experienceId,
                    'message' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    public function getExperienceAvailability(string $experienceId, Carbon $date): array
    {
        $cacheKey = "heavenly_tours_availability_{$experienceId}_{$date->format('Y-m-d')}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($experienceId, $date) {
            try {
                $pricesResponse = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/tour-prices", [
                        'tour_id' => $experienceId,
                    ]);

                $availabilityResponse = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$this->baseUrl}/api/tours/{$experienceId}/availability", [
                        'date' => $date->format('Y-m-d'),
                    ]);

                if (!$pricesResponse->successful() || !$availabilityResponse->successful()) {
                    Log::error('Heavenly Tours API error - availability', [
                        'experience_id' => $experienceId,
                        'date' => $date->format('Y-m-d'),
                        'prices_status' => $pricesResponse->status(),
                        'availability_status' => $availabilityResponse->status(),
                    ]);
                    return ['available' => false, 'prices' => []];
                }

                $prices = $pricesResponse->json();
                $availability = $availabilityResponse->json();

                return $this->normalizeAvailability($prices, $availability, $date);

            } catch (\Exception $e) {
                Log::error('Heavenly Tours API exception - availability', [
                    'experience_id' => $experienceId,
                    'date' => $date->format('Y-m-d'),
                    'message' => $e->getMessage(),
                ]);
                return ['available' => false, 'prices' => []];
            }
        });
    }

    public function getProviderName(): string
    {
        return 'Heavenly Tours';
    }

    public function isAvailable(): bool
    {
        // For demonstration purposes, always return true
        // In production, this would check the actual API
        return true;

        // Original implementation (commented out for demo)
        /*
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/tours");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
        */
    }

    private function normalizeExperiences(array $tours, Carbon $startDate, Carbon $endDate): array
    {
        $normalized = [];

        foreach ($tours as $tour) {
            $integerId = crc32($tour['id']);

            $normalized[] = [
                'id' => $integerId,
                'provider_id' => $tour['id'],
                'provider' => $this->getProviderName(),
                'slug' => $this->generateSlug($tour['title']),
                'title' => $tour['title'],
                'thumbnail' => $this->getFirstPhoto($tour['photos'] ?? []),
                'short_description' => $tour['excerpt'],
                'sell_price' => $this->getDefaultPrice($tour),
                'buy_price' => $this->getDefaultPrice($tour),
                'rating' => null,
                'city' => $tour['city'],
                'country_code' => $this->getCountryCode($tour['country']),
                'language' => 'en',
                'latitude' => null,
                'longitude' => null,
                'source' => 'heavenly_tours',
            ];
        }

        return $normalized;
    }

    private function normalizeExperienceDetails(array $tour): array
    {
        $integerId = crc32($tour['id']);

        return [
            'id' => $integerId,
            'provider_id' => $tour['id'],
            'provider' => $this->getProviderName(),
            'slug' => $this->generateSlug($tour['title']),
            'title' => $tour['title'],
            'short_description' => $tour['excerpt'],
            'description' => $tour['description'],
            'thumbnail' => $this->getFirstPhoto($tour['photos'] ?? []),
            'images' => $this->normalizeImages($tour['photos'] ?? []),
            'city' => $tour['city'],
            'country_code' => $this->getCountryCode($tour['country']),
            'language' => 'en',
            'latitude' => null,
            'longitude' => null,
            'categories' => $tour['categories'] ?? [],
            'source' => 'heavenly_tours',
        ];
    }

    private function normalizeAvailability(array $prices, array $availability, Carbon $date): array
    {
        $isAvailable = !empty($availability['available'] ?? false);
        $priceData = [];

        if ($isAvailable && !empty($prices)) {
            foreach ($prices as $price) {
                if ($price['tourId'] === $availability['tourId'] ?? null) {
                    $priceData[] = [
                        'start_time' => $date->format('Y-m-d H:i:s'),
                        'end_time' => $date->addHours(2)->format('Y-m-d H:i:s'),
                        'sell_price' => $this->extractPriceValue($price['price']),
                    ];
                }
            }
        }

        return [
            'available' => $isAvailable,
            'prices' => $priceData,
        ];
    }

    private function generateSlug(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    private function getFirstPhoto(array $photos): string
    {
        return !empty($photos) ? $photos[0] : 'https://picsum.photos/300/200';
    }

    private function normalizeImages(array $photos): array
    {
        $normalized = [];
        foreach ($photos as $index => $photo) {
            $normalized[] = [
                'url' => $photo,
                'type' => $index === 0 ? 'thumbnail' : 'gallery',
            ];
        }
        return $normalized;
    }

    private function getDefaultPrice(array $tour): string
    {
        return '99.99 USD';
    }

    private function extractPriceValue(string $priceString): float
    {
        preg_match('/\$?(\d+(?:\.\d{2})?)/', $priceString, $matches);
        return isset($matches[1]) ? (float) $matches[1] : 0.0;
    }

    private function getCountryCode(string $country): string
    {
        $countryMap = [
            'United States' => 'US',
            'Canada' => 'CA',
            'United Kingdom' => 'GB',
            'Germany' => 'DE',
            'France' => 'FR',
            'Italy' => 'IT',
            'Spain' => 'ES',
            'Turkey' => 'TR',
        ];

        return $countryMap[$country] ?? 'US';
    }

    private function getMockExperiences(): array
    {
        return [
            [
                'id' => 1,
                'provider_id' => 'mock-1',
                'provider' => 'Heavenly Tours',
                'slug' => 'mock-experience-1',
                'title' => 'Mock Experience 1',
                'thumbnail' => 'https://picsum.photos/300/200',
                'short_description' => 'Short description for Mock Experience 1',
                'sell_price' => '100.00 USD',
                'buy_price' => '100.00 USD',
                'rating' => null,
                'city' => 'Mock City',
                'country_code' => 'US',
                'language' => 'en',
                'latitude' => null,
                'longitude' => null,
                'source' => 'heavenly_tours',
            ],
            [
                'id' => 2,
                'provider_id' => 'mock-2',
                'provider' => 'Heavenly Tours',
                'slug' => 'mock-experience-2',
                'title' => 'Mock Experience 2',
                'thumbnail' => 'https://picsum.photos/300/200',
                'short_description' => 'Short description for Mock Experience 2',
                'sell_price' => '200.00 USD',
                'buy_price' => '200.00 USD',
                'rating' => null,
                'city' => 'Mock City',
                'country_code' => 'US',
                'language' => 'en',
                'latitude' => null,
                'longitude' => null,
                'source' => 'heavenly_tours',
            ],
        ];
    }
}

