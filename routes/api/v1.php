
<?php

use App\Http\Controllers\V1\ExperienceController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\TagsController;
use Illuminate\Support\Facades\Route;


Route::get('experiences', [ExperienceController::class, 'index']);
Route::get('experiences/category/{slug}', [ExperienceController::class, 'getCategoryExperiences']);
Route::get('experiences/tags/{value}', [ExperienceController::class, 'getTagExperiences']);
Route::get('experiences/details/{id}', [ExperienceController::class, 'getExperienceDetails']);
Route::get('experiences/availability', [ExperienceController::class, 'getExperienceAvailability']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);

Route::get('tags', [TagsController::class, 'index']);
