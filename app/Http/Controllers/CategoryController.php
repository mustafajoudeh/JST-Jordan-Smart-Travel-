<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminListRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    private const COUNTS = ['destinations', 'activities', 'providers', 'services', 'interestedUsers'];

    public function index(AdminListRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $query = Category::query()->withCount(self::COUNTS);
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $query->where('is_active', $filters['status'] === 'active');
        }

        if (($filters['kind'] ?? 'all') !== 'all') {
            $query->where('kind', $filters['kind']);
        }

        $sort = in_array($filters['sort'] ?? '', ['name', 'kind', 'updated_at'], true) ? $filters['sort'] : 'name';
        $records = $query->orderBy($sort, $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();

        return CategoryResource::collection($records);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated())->loadCount(self::COUNTS);

        return (new CategoryResource($category))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount(self::COUNTS));
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource|JsonResponse
    {
        $data = $request->validated();

        if ($data['kind'] !== $category->kind) {
            $dependencies = $this->dependencyCounts($category);
            if (array_sum($dependencies) > 0) {
                return response()->json([
                    'message' => 'The category type cannot change while related records use it.',
                    'dependencies' => array_filter($dependencies, fn (int $count) => $count > 0),
                ], Response::HTTP_CONFLICT);
            }
        }

        $category->update($data);

        return new CategoryResource($category->refresh()->loadCount(self::COUNTS));
    }

    public function destroy(Category $category): Response|JsonResponse
    {
        $dependencies = array_filter($this->dependencyCounts($category), fn (int $count) => $count > 0);

        if ($dependencies !== []) {
            return response()->json([
                'message' => 'This category is in use and cannot be deleted yet.',
                'dependencies' => $dependencies,
            ], Response::HTTP_CONFLICT);
        }

        try {
            $category->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'This category is linked to other records and cannot be deleted yet.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }

    private function dependencyCounts(Category $category): array
    {
        return [
            'Destinations' => $category->destinations()->count(),
            'Activities' => $category->activities()->count(),
            'Providers' => $category->providers()->count(),
            'Services' => $category->services()->count(),
            'Travelers with this interest' => $category->interestedUsers()->count(),
        ];
    }
}
