<?php
namespace App\Http\Controllers\Api\Admin\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreLessonRequest;
use App\Http\Requests\Course\UpdateLessonRequest;
use App\Http\Resources\Course\LessonResource;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Services\Course\AdminCourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class LessonController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly AdminCourseService $service) {}
    public function index(Chapter $chapter): JsonResponse { $this->authorize('viewAny', Lesson::class); return $this->successResponse(LessonResource::collection($this->service->lessons($chapter))); }
    public function store(StoreLessonRequest $request, Chapter $chapter): JsonResponse { $this->authorize('create', Lesson::class); return $this->successResponse(LessonResource::make($this->service->createLesson($chapter, $request->validated())), 'Lesson created successfully', 201); }
    public function show(Lesson $lesson): JsonResponse { $this->authorize('view', $lesson); return $this->successResponse(LessonResource::make($lesson)); }
    public function update(UpdateLessonRequest $request, Lesson $lesson): JsonResponse { $this->authorize('update', $lesson); return $this->successResponse(LessonResource::make($this->service->updateLesson($lesson, $request->validated())), 'Lesson updated successfully'); }
    public function destroy(Lesson $lesson): JsonResponse { $this->authorize('delete', $lesson); $this->service->deleteLesson($lesson); return $this->successResponse(message: 'Lesson deleted successfully'); }
}
