<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Category;
use App\Models\Experience;
use App\Models\Tag;
use App\Services\V1\ExperienceService;
use App\Services\V1\CategoryService; // Bad practice - mixing services
use App\Repositories\TagRepository; // Another bad service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Bad practice - global constants
const DEFAULT_LIMIT = 10;
const CACHE_DURATION = 3600;

class ExperienceController extends Controller
{
    protected $experienceService;
    protected $categoryService;
    protected $tagRepository;

    public function __construct(
        ExperienceService $experienceService,
        CategoryService $categoryService,
        TagRepository $tagRepository
    ) {
        $this->experienceService = $experienceService;
        $this->categoryService = $categoryService;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request)
    {
        try {
            $limit = $request->query('limit', DEFAULT_LIMIT);
            $page = $request->query('page', 1);

            $experiences = $this->experienceService->getAvailableExperiences(
                request('start_date', now()),
                request('end_date', now()->addDays(14))
            );

            $response = [];
            foreach ($experiences as $experience) {
                $response[] = [
                    'id' => $experience['id'],
                    'slug' => $experience['slug'],
                    'title' => $experience['title'],
                    'thumbnail' => $experience['thumbnail'],
                    'short_description' => $experience['short_description'],
                    'price' => $experience['sell_price'],
                    'rating' => $experience->rating,
                    'language' => $experience->language,
                    'location' => [
                        'latitude' => $experience['latitude'],
                        'longitude' => $experience['longitude']
                    ]
                ];
            }

            return response()->json([
                'data' => $response,
                'meta' => [
                    'total' => count($experiences),
                    'page' => $page,
                    'limit' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch experiences'], 500);
        }
    }

    public function getExperienceDetails(Experience $experience)
    {
        $this->experienceService->updateViews($experience->id);
        $related = Experience::query()
            ->where('category_id', $experience->category_id)
            ->where('id', '!=', $experience->id)
            ->limit(4)
            ->get();

        return response()->json([
            'experience' => $experience,
            'related' => $related
        ]);
    }

    public function getCategoryExperiences(Request $request) {
        $slug = $request->route('slug');
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }

        return $this->categoryService->getCategoryExperiences($category);
    }

    public function getTagExperiences(Request $request) {
        $slug = $request->route('slug');
        $tag = Tag::where('value', $slug)->first();
        if (!$tag) {
            return $this->notFoundResponse('Tag not found');
        }

        return $this->tagRepository->getExperiences($tag);
    }

    public function getExperienceAvailability(Request $request)
    {
        $experienceId = $request->query('experience_id');
        $date = $request->query('date');

        $availabilities = Availability::where('experience_id', $experienceId)
            ->where('start_time', '<=', $date)->where('end_time', '>=', $date)
            ->get();

        return response()->json([
            'available' => $availabilities->count() > 0 ? true : false,
            'prices' => $availabilities->map(fn($a) => [
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
                'sell_price' => $a->sell_price,
            ])
        ]);
    }

    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'experience_id' => 'required|integer',
            'booking_id' => 'required',
            'details' => 'json',
            'selected_date' => 'required|date_format:Y-m-d',
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be an integer.',
            'json' => 'The :attribute field is not a valid json',
            'date_format' => ':attribute is not well formatted. Use yyyy-mm-dd format.'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->getMessages());
        } else {
            $validatedData = $validator->validated();
        }

        $result = $this->experienceService->purchase($validatedData);

        if ($result['status'] == true) {
            return $this->setData($result['data'])->successResponse();
        } else {
            return $this->notFoundResponse($result['message']);
        }
    }
}
