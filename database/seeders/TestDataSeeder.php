<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Availability;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Adventure Tours', 'slug' => 'adventure-tours', 'description' => 'Exciting adventure experiences', 'image' => 'https://picsum.photos/300/200?random=10', 'is_active' => true, 'priority' => 'high'],
            ['name' => 'Cultural Tours', 'slug' => 'cultural-tours', 'description' => 'Cultural and historical experiences', 'image' => 'https://picsum.photos/300/200?random=11', 'is_active' => true, 'priority' => 'medium'],
            ['name' => 'Food Tours', 'slug' => 'food-tours', 'description' => 'Culinary experiences', 'image' => 'https://picsum.photos/300/200?random=12', 'is_active' => true, 'priority' => 'medium'],
            ['name' => 'City Tours', 'slug' => 'city-tours', 'description' => 'Urban exploration experiences', 'image' => 'https://picsum.photos/300/200?random=13', 'is_active' => true, 'priority' => 'high'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        $tags = [
            ['value' => 'outdoor'],
            ['value' => 'indoor'],
            ['value' => 'family-friendly'],
            ['value' => 'romantic'],
            ['value' => 'budget'],
            ['value' => 'luxury'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        $experiences = [
            [
                'slug' => 'new-york-city-tour',
                'title' => 'New York City Walking Tour',
                'short_description' => 'Explore the Big Apple on foot with our expert guides',
                'description' => 'Discover the hidden gems of New York City with our comprehensive walking tour. Visit iconic landmarks, learn about the city\'s rich history, and experience the vibrant culture of Manhattan.',
                'language' => 'en',
                'inclusions' => 'Professional guide, bottled water, map',
                'exclusions' => 'Food, transportation, gratuities',
                'itinerary' => 'Times Square → Broadway → Central Park → Empire State Building',
                'what_to_bring' => 'Comfortable walking shoes, camera, water',
                'what_to_wear' => 'Casual clothing suitable for walking',
                'what_to_expect' => '3-hour walking tour covering 2-3 miles',
                'what_to_know' => 'Tour operates rain or shine, maximum 15 participants',
                'remarks' => 'Please arrive 15 minutes before start time',
                'meeting_instructions' => 'Meet at Times Square Visitor Center',
                'cancellation_policy' => 'Free cancellation up to 24 hours before',
                'refund_policy' => 'Full refund for cancellations made 24+ hours in advance',
                'health_and_safety' => 'Masks recommended in crowded areas',
                'thumbnail' => 'https://picsum.photos/300/200?random=1',
                'latitude' => 40.7580,
                'longitude' => -73.9855,
                'city_id' => 'New York',
                'country_code' => 'US',
                'rating' => 4.8,
                'views' => 1250,
                'is_active' => true,
            ],
            [
                'slug' => 'paris-food-tour',
                'title' => 'Paris Food & Wine Tour',
                'short_description' => 'Taste the best of French cuisine in the City of Light',
                'description' => 'Embark on a culinary journey through Paris, sampling authentic French cuisine, fine wines, and artisanal pastries. Visit local markets, bakeries, and wine shops.',
                'language' => 'en',
                'inclusions' => 'Food tastings, wine samples, expert guide',
                'exclusions' => 'Additional food and drinks, transportation',
                'itinerary' => 'Le Marais → Latin Quarter → Saint-Germain-des-Prés',
                'what_to_bring' => 'Appetite for good food, comfortable shoes',
                'what_to_wear' => 'Smart casual attire',
                'what_to_expect' => '4-hour food tour with 6-8 tastings',
                'what_to_know' => 'Vegetarian options available, maximum 12 participants',
                'remarks' => 'Dietary restrictions must be notified in advance',
                'meeting_instructions' => 'Meet at Place des Vosges',
                'cancellation_policy' => 'Free cancellation up to 48 hours before',
                'refund_policy' => 'Full refund for cancellations made 48+ hours in advance',
                'health_and_safety' => 'All food establishments follow health guidelines',
                'thumbnail' => 'https://picsum.photos/300/200?random=2',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'city_id' => 'Paris',
                'country_code' => 'FR',
                'rating' => 4.9,
                'views' => 890,
                'is_active' => true,
            ],
            [
                'slug' => 'tokyo-culture-tour',
                'title' => 'Tokyo Cultural Experience',
                'short_description' => 'Immerse yourself in Japanese culture and traditions',
                'description' => 'Experience the perfect blend of ancient traditions and modern innovation in Tokyo. Visit temples, participate in tea ceremonies, and learn about Japanese customs.',
                'language' => 'en',
                'inclusions' => 'Cultural activities, tea ceremony, temple visits',
                'exclusions' => 'Transportation, personal expenses',
                'itinerary' => 'Senso-ji Temple → Tea Ceremony → Traditional Garden',
                'what_to_bring' => 'Respectful attitude, camera',
                'what_to_wear' => 'Modest clothing covering shoulders and knees',
                'what_to_expect' => '5-hour cultural immersion experience',
                'what_to_know' => 'Shoes must be removed at temples, maximum 10 participants',
                'remarks' => 'Please be respectful of local customs',
                'meeting_instructions' => 'Meet at Asakusa Station',
                'cancellation_policy' => 'Free cancellation up to 72 hours before',
                'refund_policy' => 'Full refund for cancellations made 72+ hours in advance',
                'health_and_safety' => 'Temperature checks may be required',
                'thumbnail' => 'https://picsum.photos/300/200?random=3',
                'latitude' => 35.6762,
                'longitude' => 139.6503,
                'city_id' => 'Tokyo',
                'country_code' => 'JP',
                'rating' => 4.7,
                'views' => 650,
                'is_active' => true,
            ],
        ];

        foreach ($experiences as $experienceData) {
            $experience = Experience::create($experienceData);

            $startDate = Carbon::now();
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);

                $timeSlots = [
                    ['09:00', '12:00', 99.99, 79.99],
                    ['14:00', '17:00', 89.99, 69.99],
                    ['18:00', '21:00', 109.99, 89.99],
                ];

                foreach ($timeSlots as [$startTime, $endTime, $sellPrice, $buyPrice]) {
                    Availability::create([
                        'experience_id' => $experience->id,
                        'sell_price' => $sellPrice,
                        'buy_price' => $buyPrice,
                        'start_time' => $date->copy()->setTimeFromTimeString($startTime),
                        'end_time' => $date->copy()->setTimeFromTimeString($endTime),
                    ]);
                }
            }

            $experience->categories()->attach(Category::inRandomOrder()->first()->id);
            $experience->tags()->attach(Tag::inRandomOrder()->limit(3)->pluck('id'));
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Created ' . count($experiences) . ' experiences with availabilities');
    }
}
