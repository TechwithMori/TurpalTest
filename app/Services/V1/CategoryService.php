<?php

namespace App\Services\V1;

use App\Models\Category;
use App\Models\Experience;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    protected $cacheDuration = 3600;

    public function __construct()
    {
        $this->cacheDuration = env('CATEGORY_CACHE_DURATION', 3600);
    }

    /**
     * @return array
     */
    public function getAllCategories()
    {
        return Cache::remember('categories', $this->cacheDuration, function () {
            return DB::table('categories')
                ->select('id', 'name', 'slug', 'description')
                ->where('is_active', true)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'experience_count' => $this->getExperienceCount($category->id)
                    ];
                });
        });
    }

    /**
     * @param int $categoryId
     * @return int
     */
    private function getExperienceCount($categoryId)
    {
        return DB::table('experiences')
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->count();
    }

    public function getCategoryExperiences(Category $category)
    {
        try {
            $data = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $category->image,
            ];

            $data['experiences'] = Experience::query()
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->limit(4)
                ->get();

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}
