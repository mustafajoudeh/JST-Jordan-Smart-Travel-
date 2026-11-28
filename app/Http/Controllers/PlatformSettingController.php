<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminListRequest;
use App\Http\Requests\Admin\StorePlatformSettingRequest;
use App\Http\Requests\Admin\UpdatePlatformSettingRequest;
use App\Http\Resources\PlatformSettingResource;
use App\Models\PlatformSetting;
use App\Support\Admin\PlatformSettingCatalog;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PlatformSettingController extends Controller
{
    public function index(AdminListRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $query = PlatformSetting::query();
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('key', 'like', "%{$search}%")
                ->orWhereRaw("REPLACE(`key`, '_', ' ') LIKE ?", ["%{$search}%"])
                ->orWhere('description', 'like', "%{$search}%"));
        }

        $group = $filters['group'] ?? 'all';
        if ($group !== 'all') {
            $keys = array_keys(array_filter(
                array_combine(PlatformSettingCatalog::protectedKeys(), array_map(
                    fn (string $key) => PlatformSettingCatalog::definition($key)['group'],
                    PlatformSettingCatalog::protectedKeys(),
                )),
                fn (string $value) => $value === $group,
            ));

            if ($group === 'other') {
                $query->whereNotIn('key', PlatformSettingCatalog::protectedKeys());
            } elseif ($keys !== []) {
                $query->whereIn('key', $keys);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $sort = in_array($filters['sort'] ?? '', ['key', 'updated_at'], true) ? $filters['sort'] : 'key';
        $records = $query->orderBy($sort, $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();

        return PlatformSettingResource::collection($records);
    }

    public function store(StorePlatformSettingRequest $request): JsonResponse
    {
        $key = Str::slug($request->validated('name'), '_');
        $data = $request->safe()->only(['value', 'value_type', 'description']);

        try {
            $setting = DB::transaction(fn () => PlatformSetting::create([
                'key' => $key,
                ...$data,
                'updated_by' => $request->user()->id,
            ]));
        } catch (QueryException) {
            return response()->json([
                'message' => 'A setting with this name already exists.',
            ], Response::HTTP_CONFLICT);
        }

        return (new PlatformSettingResource($setting))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PlatformSetting $setting): PlatformSettingResource
    {
        return new PlatformSettingResource($setting);
    }

    public function update(UpdatePlatformSettingRequest $request, PlatformSetting $setting): PlatformSettingResource
    {
        $data = $request->safe()->only(['value_type', 'value', 'description']);
        if (PlatformSettingCatalog::isSensitive($setting->key) && (string) ($data['value'] ?? '') === '') {
            unset($data['value']);
        }
        $data['updated_by'] = $request->user()->id;
        $setting->update($data);

        return new PlatformSettingResource($setting->refresh());
    }

    public function destroy(PlatformSetting $setting): Response|JsonResponse
    {
        if (PlatformSettingCatalog::isProtected($setting->key)) {
            return response()->json([
                'message' => 'This core setting is required by the platform and cannot be deleted.',
            ], Response::HTTP_CONFLICT);
        }

        $setting->delete();

        return response()->noContent();
    }
}
