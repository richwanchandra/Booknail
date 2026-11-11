<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminServiceBase64ImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_service_image_is_stored_as_base64_and_not_on_disk(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::create([
            'name' => 'Nail Gel Art',
            'slug' => 'nail-gel-art',
            'is_active' => true,
        ]);

        $fakeImage = UploadedFile::fake()->image('nailart.png', 100, 100);

        $response = $this->actingAs($admin)->post(route('admin.services.store'), [
            'name' => 'Luxury Glitter Gel',
            'category_id' => $category->id,
            'price' => 150000,
            'duration_minutes' => 60,
            'description' => 'Layanan nail art glitter elegan.',
            'service_image' => $fakeImage,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $response->assertSessionHas('success');

        $service = Service::where('name', 'Luxury Glitter Gel')->first();
        $this->assertNotNull($service);

        // Check that image starts with Base64 data URL
        $this->assertNotNull($service->image);
        $this->assertStringStartsWith('data:image/', $service->image);
        $this->assertStringContainsString(';base64,', $service->image);

        // Verify service_image alias accessor works
        $this->assertEquals($service->image, $service->service_image);

        // Verify NO files were created on public storage
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_service_image_update_saves_new_base64(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::create([
            'name' => 'Pedicure',
            'slug' => 'pedicure',
            'is_active' => true,
        ]);

        $service = Service::create([
            'name' => 'Spa Pedicure',
            'category_id' => $category->id,
            'price' => 120000,
            'duration_minutes' => 45,
            'description' => 'Pedicure santai',
            'image' => null,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('pedicure_new.jpg', 150, 150);

        $response = $this->actingAs($admin)->put(route('admin.services.update', $service), [
            'name' => 'Spa Pedicure Updated',
            'category_id' => $category->id,
            'price' => 135000,
            'duration_minutes' => 50,
            'description' => 'Pedicure santai premium',
            'service_image' => $newImage,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $service->refresh();

        $this->assertEquals('Spa Pedicure Updated', $service->name);
        $this->assertNotNull($service->image);
        $this->assertStringStartsWith('data:image/', $service->image);
        $this->assertStringContainsString(';base64,', $service->image);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }
}
