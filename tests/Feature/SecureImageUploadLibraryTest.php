<?php

namespace Tests\Feature;

use App\Livewire\Uploads\SecureImageUpload;
use App\Models\User;
use App\Services\CloudinaryUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class SecureImageUploadLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Livewire's isolated component test harness dispatches each ->call()
        // through a middleware-less internal request (see
        // Livewire\Features\SupportTesting\RequestBroker), so `StartSession`
        // never runs and request()->session() throws. SecureImageUpload
        // relies on request()->session() via LivewireSecureUploads, so bind
        // a session store onto every request the container rebinds — which
        // is what the real StartSession middleware would have done.
        $session = $this->app['session']->driver();
        $this->app['request']->setLaravelSession($session);
        $this->app->rebinding('request', function ($app, $request) use ($session) {
            $request->setLaravelSession($session);
        });
    }

    public function test_selecting_from_library_attaches_the_asset_and_marks_it_shared(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin)->withSession([]);

        Livewire::test(SecureImageUpload::class, ['inputName' => 'featured_image_path'])
            ->set('isCloudinary', true)
            ->call('selectFromLibrary', 'printbuka/product-images/existing-asset')
            ->assertSet('storedPath', 'printbuka/product-images/existing-asset')
            ->assertSet('storedPathFromLibrary', true);

        $this->assertDatabaseHas('shared_media_assets', ['public_id' => 'printbuka/product-images/existing-asset']);
    }

    public function test_replacing_a_library_sourced_image_with_another_never_deletes_it_from_cloudinary(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin)->withSession([]);

        $cloudinaryService = Mockery::mock(CloudinaryUploadService::class);
        $cloudinaryService->shouldNotReceive('delete');
        $this->app->instance(CloudinaryUploadService::class, $cloudinaryService);

        Livewire::test(SecureImageUpload::class, ['inputName' => 'featured_image_path'])
            ->set('isCloudinary', true)
            ->call('selectFromLibrary', 'printbuka/shared/first-pick')
            ->call('selectFromLibrary', 'printbuka/shared/second-pick')
            ->assertSet('storedPath', 'printbuka/shared/second-pick')
            ->assertSet('storedPathFromLibrary', true);
    }

    public function test_clearing_a_library_sourced_image_never_deletes_it_from_cloudinary(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin)->withSession([]);

        $cloudinaryService = Mockery::mock(CloudinaryUploadService::class);
        $cloudinaryService->shouldNotReceive('delete');
        $this->app->instance(CloudinaryUploadService::class, $cloudinaryService);

        Livewire::test(SecureImageUpload::class, ['inputName' => 'featured_image_path'])
            ->set('isCloudinary', true)
            ->call('selectFromLibrary', 'printbuka/shared/asset')
            ->call('clearSingle')
            ->assertSet('storedPath', null);
    }

    public function test_replacing_an_exclusively_owned_image_with_a_library_pick_still_deletes_the_old_one(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin)->withSession([]);

        $cloudinaryService = Mockery::mock(CloudinaryUploadService::class);
        $cloudinaryService->shouldReceive('delete')->once()->with('printbuka/owned/old-asset');
        $this->app->instance(CloudinaryUploadService::class, $cloudinaryService);

        Livewire::test(SecureImageUpload::class, ['inputName' => 'featured_image_path'])
            ->set('isCloudinary', true)
            ->set('storedPath', 'printbuka/owned/old-asset')
            ->set('storedPathFromLibrary', false)
            ->call('selectFromLibrary', 'printbuka/shared/new-pick')
            ->assertSet('storedPath', 'printbuka/shared/new-pick');
    }

    public function test_selecting_from_library_respects_max_files_in_multiple_mode(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin)->withSession([]);

        Livewire::test(SecureImageUpload::class, [
            'inputName' => 'additional_image_paths',
            'multiple' => true,
            'maxFiles' => 1,
        ])
            ->set('isCloudinary', true)
            ->call('selectFromLibrary', 'printbuka/gallery/one')
            ->call('selectFromLibrary', 'printbuka/gallery/two')
            ->assertSet('storedPaths', ['printbuka/gallery/one'])
            ->assertHasErrors('uploads');
    }
}
