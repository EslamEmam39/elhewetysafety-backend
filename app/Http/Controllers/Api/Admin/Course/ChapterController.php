<?php
namespace App\Http\Controllers\Api\Admin\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreChapterRequest;
use App\Http\Requests\Course\UpdateChapterRequest;
use App\Http\Resources\Course\ChapterResource;
use App\Models\Chapter;
use App\Models\Course;
use App\Services\Course\AdminCourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class ChapterController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly AdminCourseService $service) {}
    public function index(Course $course): JsonResponse { $this->authorize('viewAny', Chapter::class); return $this->successResponse(ChapterResource::collection($this->service->chapters($course))); }
    public function store(StoreChapterRequest $request, Course $course): JsonResponse { $this->authorize('create', Chapter::class); return $this->successResponse(ChapterResource::make($this->service->createChapter($course, $request->validated())), 'Chapter created successfully', 201); }
    public function show(Chapter $chapter): JsonResponse { $this->authorize('view', $chapter); return $this->successResponse(ChapterResource::make($chapter->load('lessons'))); }
    public function update(UpdateChapterRequest $request, Chapter $chapter): JsonResponse { $this->authorize('update', $chapter); return $this->successResponse(ChapterResource::make($this->service->updateChapter($chapter, $request->validated())), 'Chapter updated successfully'); }
    public function destroy(Chapter $chapter): JsonResponse { $this->authorize('delete', $chapter); $this->service->deleteChapter($chapter); return $this->successResponse(message: 'Chapter deleted successfully'); }
}
