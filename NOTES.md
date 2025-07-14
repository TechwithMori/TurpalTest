# Travel API Integration - Simple Testing Guide

## What We Built
- Integrated Heavenly Tours provider into the existing Laravel API
- Added provider abstraction layer with caching and error handling
- Maintained existing API endpoints while adding provider data transparently

## Simple Testing Method: PowerShell

### Step 1: Start Laravel Server
```powershell
php artisan serve
```
Keep this running in a separate PowerShell window.

### Step 2: Test the Integration
Open a new PowerShell window and run:

```powershell
# Test 1: Get all experiences (should include both local and provider data)
Invoke-RestMethod -Uri "http://localhost:8000/api/v1/experiences" -Method GET

# Test 2: Get a specific experience by ID
Invoke-RestMethod -Uri "http://localhost:8000/api/v1/experiences/1" -Method GET

# Test 3: Get experiences with provider data (new endpoint)
Invoke-RestMethod -Uri "http://localhost:8000/api/v1/experiences?include_provider=true" -Method GET
```

### Step 3: Expected Results

**Test 1 & 2**: Should return experiences with this structure:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Experience Title",
      "description": "Description...",
      "price": 100.00,
      "duration": "2 hours",
      "location": "City, Country",
      "provider_data": {
        "provider": "heavenly_tours",
        "external_id": "HT001",
        "rating": 4.5,
        "reviews_count": 25
      }
    }
  ]
}
```

**Test 3**: Should return experiences with additional provider information.

### Step 4: Test Provider Configuration
```powershell
# Test provider configuration
php artisan test:provider
```

This should show:
- Provider connection status
- Sample provider data
- Cache status

## Troubleshooting

**If you get "No experiences found"**:
- Run the database seeder: `php artisan db:seed`
- Check database connection

**If provider data is missing**:
- Check `.env` file has `HEAVENLY_TOURS_API_URL` and `HEAVENLY_TOURS_API_KEY`
- Run `php artisan config:clear`

## What's Different Now
- API responses include `provider_data` field
- New query parameter `?include_provider=true` for detailed provider info
- Automatic caching of provider responses
- Graceful fallback if provider is unavailable (The scenario that is woeking based on the fictionale APIs on mock.turpal.com)
 