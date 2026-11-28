<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Governorate;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTaxonomySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_module_permissions_follow_their_roles(): void
    {
        $systemAdmin = User::factory()->systemAdmin()->create();
        $contentAdmin = User::factory()->contentAdmin()->create();
        $provider = User::factory()->provider()->create();
        $traveler = User::factory()->create();

        $this->withToken($systemAdmin->createToken('taxonomy-admin')->plainTextToken)
            ->getJson('/api/admin/governorates')->assertOk();
        $this->withToken($contentAdmin->createToken('taxonomy-content')->plainTextToken)
            ->getJson('/api/admin/categories')->assertOk();
        $this->withToken($contentAdmin->createToken('settings-denied')->plainTextToken)
            ->getJson('/api/admin/platform-settings')->assertForbidden();
        $this->withToken($provider->createToken('taxonomy-provider')->plainTextToken)
            ->getJson('/api/admin/governorates')->assertForbidden();
        $this->withToken($traveler->createToken('taxonomy-traveler')->plainTextToken)
            ->getJson('/api/admin/categories')->assertForbidden();
    }

    public function test_governorates_support_search_pagination_crud_and_dependency_protection(): void
    {
        $admin = User::factory()->systemAdmin()->create();
        $token = $admin->createToken('governorate-crud')->plainTextToken;
        Governorate::factory()->create(['name' => 'Amman']);
        Governorate::factory()->create(['name' => 'Aqaba']);

        $this->withToken($token)->getJson('/api/admin/governorates?search=amman&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Amman')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonMissingPath('data.0.id');

        $this->withToken($token)->postJson('/api/admin/governorates', [
            'name' => 'Ajloun',
            'description' => 'Northern highlands',
            'is_active' => true,
        ])->assertCreated()->assertJsonPath('data.name', 'Ajloun');
        $created = Governorate::query()->where('name', 'Ajloun')->firstOrFail();

        $this->withToken($token)->putJson("/api/admin/governorates/{$created->id}", [
            'name' => 'Ajloun Highlands',
            'description' => 'Updated overview',
            'is_active' => false,
        ])->assertOk()->assertJsonPath('data.is_active', false);

        $this->withToken($token)->postJson('/api/admin/governorates', [
            'name' => 'Amman',
            'is_active' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('name');

        $destination = Destination::factory()->create(['governorate_id' => $created->id]);
        $this->withToken($token)->deleteJson("/api/admin/governorates/{$created->id}")
            ->assertConflict()
            ->assertJsonPath('dependencies.Destinations', 1);

        $this->assertDatabaseHas('destinations', ['id' => $destination->id]);
        $unused = Governorate::factory()->create(['name' => 'Tafilah']);
        $this->withToken($token)->deleteJson("/api/admin/governorates/{$unused->id}")->assertNoContent();
        $this->assertDatabaseMissing('governorates', ['id' => $unused->id]);
    }

    public function test_categories_support_kind_filters_crud_and_dependency_protection(): void
    {
        $admin = User::factory()->contentAdmin()->create();
        $token = $admin->createToken('category-crud')->plainTextToken;
        Category::factory()->create(['kind' => 'tourism', 'name' => 'Nature walks']);
        Category::factory()->create(['kind' => 'service', 'name' => 'Local transport']);

        $this->withToken($token)->getJson('/api/admin/categories?kind=service&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Local transport')
            ->assertJsonPath('meta.total', 1);

        $this->withToken($token)->postJson('/api/admin/categories', [
            'kind' => 'tourism',
            'name' => 'Architecture',
            'description' => 'Built heritage',
            'is_active' => true,
        ])->assertCreated()->assertJsonPath('data.kind', 'tourism');
        $created = Category::query()->where('name', 'Architecture')->firstOrFail();

        $this->withToken($token)->putJson("/api/admin/categories/{$created->id}", [
            'kind' => 'service',
            'name' => 'Architecture services',
            'description' => 'Updated classification',
            'is_active' => false,
        ])->assertOk()->assertJsonPath('data.kind', 'service');

        $this->withToken($token)->postJson('/api/admin/categories', [
            'kind' => 'service',
            'name' => 'Architecture services',
            'is_active' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('name');

        $used = Category::factory()->tourism()->create();
        Activity::factory()->create(['category_id' => $used->id]);
        $this->withToken($token)->putJson("/api/admin/categories/{$used->id}", [
            'kind' => 'service',
            'name' => $used->name,
            'is_active' => true,
        ])->assertConflict();
        $this->withToken($token)->deleteJson("/api/admin/categories/{$used->id}")
            ->assertConflict()
            ->assertJsonPath('dependencies.Activities', 1);

        $unused = Category::factory()->tourism()->create();
        $this->withToken($token)->deleteJson("/api/admin/categories/{$unused->id}")->assertNoContent();
        $this->assertDatabaseMissing('categories', ['id' => $unused->id]);
    }

    public function test_platform_settings_validate_protect_core_records_and_never_return_secret_values(): void
    {
        $admin = User::factory()->systemAdmin()->create();
        $token = $admin->createToken('settings-crud')->plainTextToken;
        $core = PlatformSetting::factory()->create([
            'key' => 'default_currency',
            'value' => 'JOD',
            'value_type' => 'string',
            'description' => 'Platform currency',
        ]);

        $this->withToken($token)->getJson('/api/admin/platform-settings?group=booking_finance')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Default currency')
            ->assertJsonMissingPath('data.0.id');
        $this->withToken($token)->putJson('/api/admin/platform-settings/default_currency', [
            'value_type' => 'integer',
            'value' => '12',
        ])->assertUnprocessable()->assertJsonValidationErrors('value_type');
        $this->withToken($token)->putJson('/api/admin/platform-settings/default_currency', [
            'value_type' => 'string',
            'value' => 'USD',
            'description' => 'Updated currency',
        ])->assertOk()->assertJsonPath('data.value', 'USD');
        $this->withToken($token)->deleteJson('/api/admin/platform-settings/default_currency')->assertConflict();

        $secret = PlatformSetting::factory()->create([
            'key' => 'payment_api_token',
            'value' => 'private-test-value',
            'value_type' => 'string',
        ]);
        $this->withToken($token)->getJson('/api/admin/platform-settings/payment_api_token')
            ->assertOk()
            ->assertJsonPath('data.value', null)
            ->assertJsonPath('data.has_value', true)
            ->assertDontSee('private-test-value');
        $this->withToken($token)->putJson('/api/admin/platform-settings/payment_api_token', [
            'value_type' => 'string',
            'value' => '',
            'description' => 'Keep the saved token',
        ])->assertOk()->assertJsonPath('data.value', null);
        $this->assertDatabaseHas('platform_settings', [
            'key' => 'payment_api_token',
            'value' => 'private-test-value',
        ]);

        $this->withToken($token)->postJson('/api/admin/platform-settings', [
            'name' => 'Local trip buffer',
            'value_type' => 'integer',
            'value' => 'not-a-number',
        ])->assertUnprocessable()->assertJsonValidationErrors('value');
        $created = $this->withToken($token)->postJson('/api/admin/platform-settings', [
            'name' => 'Local trip buffer',
            'value_type' => 'integer',
            'value' => '2',
            'description' => 'Local buffer used by operations',
        ])->assertCreated()->assertJsonPath('data.name', 'Local trip buffer');
        $key = $created->json('data.key');
        $this->withToken($token)->deleteJson("/api/admin/platform-settings/{$key}")->assertNoContent();
        $this->assertDatabaseMissing('platform_settings', ['key' => $key]);
        $this->assertDatabaseHas('platform_settings', ['key' => $core->key, 'value' => 'USD']);
        $this->assertDatabaseHas('platform_settings', ['key' => $secret->key, 'value' => 'private-test-value']);
    }
}
