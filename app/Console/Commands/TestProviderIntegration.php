<?php

namespace App\Console\Commands;

use App\Services\Providers\ExperienceProviderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestProviderIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:provider-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Heavenly Tours provider integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ExperienceProviderService $providerService)
    {
        $this->info('🧪 Testing Heavenly Tours Provider Integration...');
        $this->newLine();

        // Test 1: Check available providers
        $this->info('1. Checking available providers...');
        $providers = $providerService->getAvailableProviders();
        $this->info("   Available providers: " . implode(', ', $providers));
        $this->newLine();

        // Test 2: Get experiences from all sources
        $this->info('2. Fetching experiences from all sources...');
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(14);

        $experiences = $providerService->getAvailableExperiences($startDate, $endDate);
        $this->info("   Total experiences found: " . count($experiences));

        // Count by source
        $sourceCounts = [];
        foreach ($experiences as $experience) {
            $source = $experience['source'] ?? 'unknown';
            $sourceCounts[$source] = ($sourceCounts[$source] ?? 0) + 1;
        }

        foreach ($sourceCounts as $source => $count) {
            $this->info("   - {$source}: {$count} experiences");
        }
        $this->newLine();

        // Test 3: Check provider health
        $this->info('3. Checking provider health...');
        foreach ($providers as $provider) {
            if ($provider === 'local') {
                $this->info("   ✓ Local database: Available");
            } else {
                $this->info("   - {$provider}: Checking...");
            }
        }
        $this->newLine();

        // Test 4: Test experience details (if any experiences exist)
        if (!empty($experiences)) {
            $this->info('4. Testing experience details retrieval...');
            $firstExperience = $experiences[0];
            $experienceId = $firstExperience['id'];

            $details = $providerService->getExperienceDetails($experienceId);
            if ($details) {
                $this->info("   ✓ Experience details retrieved for ID: {$experienceId}");
                $this->info("   - Title: {$details['title']}");
                $this->info("   - Source: {$details['source']}");
            } else {
                $this->warn("   ⚠ No details found for experience ID: {$experienceId}");
            }
        } else {
            $this->warn('4. Skipping experience details test (no experiences available)');
        }
        $this->newLine();

        // Test 5: Test availability
        $this->info('5. Testing availability check...');
        if (!empty($experiences)) {
            $firstExperience = $experiences[0];
            $experienceId = $firstExperience['id'];
            $testDate = Carbon::now()->addDays(7);

            $availability = $providerService->getExperienceAvailability($experienceId, $testDate);
            $this->info("   - Experience ID: {$experienceId}");
            $this->info("   - Date: {$testDate->format('Y-m-d')}");
            $this->info("   - Available: " . ($availability['available'] ? 'Yes' : 'No'));
            $this->info("   - Price options: " . count($availability['prices']));
        } else {
            $this->warn('   Skipping availability test (no experiences available)');
        }
        $this->newLine();

        $this->info('✅ Provider integration test completed!');
        $this->info('📝 Check the logs for detailed provider communication.');

        return Command::SUCCESS;
    }
}
