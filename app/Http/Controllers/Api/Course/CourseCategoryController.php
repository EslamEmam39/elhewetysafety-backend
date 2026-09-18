<?php
namespace App\Http\Controllers\Api\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseCategoryResource;
use App\Services\Course\CourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class CourseCategoryController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly CourseService $service) {}
    public function index(): JsonResponse
    {
        return $this->successResponse(CourseCategoryResource::collection($this->service->activeCategories()));
    }
}
