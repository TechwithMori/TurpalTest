<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TagRepository
{
    protected $cacheDuration = 86400;

    public function __construct()
    {
        $this->cacheDuration = env('TAG_CACHE_DURATION', 86400);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return Cache::remember('tags', $this->cacheDuration, function () {
            return Tag::query()
                ->select('id', 'value')
                ->where('is_active', true)
                ->get()
                ->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'experience_count' => $this->getExperienceCount($tag->id)
                    ];
                });
        });
    }

    /**
     * @param int $tagId
     * @return int
     */
    private function getExperienceCount($tagId)
    {
        return DB::table('experience_tags')
            ->where('tag_id', $tagId)
            ->join('experiences', 'experiences.id', '=', 'experience_tags.experience_id')
            ->where('experiences.is_active', true)
            ->count();
    }

    /**
     * @param int $tagId
     * @return array|null
     */
    public function getExperiences(Tag $tag)
    {
        try {
            $data = [
                'id' => $tag->id,
                'value' => $tag->value,
            ];

            // Bad practice - direct DB query
            $data['experiences'] = DB::table('experiences')
                ->join('experience_tags', 'experiences.id', '=', 'experience_tags.experience_id')
                ->where('experience_tags.tag_id', $tag->id)
                ->where('experiences.is_active', true)
                ->limit(4)
                ->get();

            return $data;
        } catch (\Exception $e) {
            // Bad error handling
            return null;
        }
    }
}
