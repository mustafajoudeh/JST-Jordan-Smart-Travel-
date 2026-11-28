<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminListRequest;
use App\Http\Requests\Admin\StoreGovernorateRequest;
use App\Http\Requests\Admin\UpdateGovernorateRequest;
use App\Http\Resources\GovernorateResource;
use App\Models\Governorate;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class GovernorateController extends Controller
{
    private const COUNTS = ['destinations', 'events', 'providers'];

    public function index(AdminListRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $query = Governorate::query()->withCount(self::COUNTS);
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $query->where('is_active', $filters['status'] === 'active');
        }

        $sort = in_array($filters['sort'] ?? '', ['name', 'updated_at'], true) ? $filters['sort'] : 'name';
        $records = $query->orderBy($sort, $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();

        return GovernorateResource::collection($records);
    }

    public function store(StoreGovernorateRequest $request): JsonResponse
    {
        $governorate = Governorate::create($request->validated())->loadCount(self::COUNTS);

        return (new GovernorateResource($governorate))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Governorate $governorate): GovernorateResource
    {
        return new GovernorateResource($governorate->loadCount(self::COUNTS));
    }

    public function update(UpdateGovernorateRequest $request, Governorate $governorate): GovernorateResource
    {
        $governorate->update($request->validated());

        return new GovernorateResource($governorate->refresh()->loadCount(self::COUNTS));
    }

    public function destroy(Governorate $governorate): Response|JsonResponse
    {
        $dependencies = [
            'Destinations' => $governorate->destinations()->count(),
            'Events' => $governorate->events()->count(),
            'Providers' => $governorate->providers()->count(),
        ];
        $dependencies = array_filter($dependencies, fn (int $count) => $count > 0);

        if ($dependencies !== []) {
            return response()->json([
                'message' => 'This governorate is in use and cannot be deleted yet.',
                'dependencies' => $dependencies,
            ], Response::HTTP_CONFLICT);
        }

        try {
            $governorate->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'This governorate is linked to other records and cannot be deleted yet.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->noContent();
    }
}
