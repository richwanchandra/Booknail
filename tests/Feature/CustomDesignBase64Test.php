<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomDesignBase64Test extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected NailArtist $artist;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);

        $this->customer = User::factory()->create([
            'phone' => '081234567890',
            'loyalty_points' => 0,
        ]);
        $this->customer->assignRole('customer');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $artistUser = User::factory()->create();
        $artistUser->assignRole('artist');
        $this->artist = NailArtist::create([
            'user_id' => $artistUser->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Nail Art Category',
            'slug' => 'nail-art-category',
        ]);

        $this->service = Service::create([
            'category_id' => $category->id,
            'name' => 'Gel Nail Art Extension',
            'slug' => 'gel-nail-art-extension',
            'price' => 150000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    public function test_design_photo_upload_is_converted_to_base64_in_session()
    {
        $step1Data = [
            'service_ids' => [$this->service->id],
            'artist_id' => $this->artist->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00',
            'total_duration' => 60,
            'total_price' => 150000,
        ];

        $designFile = UploadedFile::fake()->image('my_custom_nail_design.png', 800, 800);

        $response = $this->actingAs($this->customer)
            ->withSession([
                'booking_step1' => $step1Data,
                'booking_current_step' => 3,
            ])
            ->post(route('booking.uploadDesign'), [
                'design_photo' => $designFile,
                'design_notes' => 'Tolong buatkan gradasi warna nude pink dengan glitter silver',
            ]);

        $response->assertSessionHas('success');
        $response->assertSessionHas('booking_step2', function ($step2) {
            return !empty($step2['design_photo_base64'])
                && str_starts_with($step2['design_photo_base64'], 'data:image/png;base64,')
                && $step2['design_notes'] === 'Tolong buatkan gradasi warna nude pink dengan glitter silver';
        });
    }

    public function test_booking_with_base64_design_is_previewed_in_admin_and_artist_pages()
    {
        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $booking = Booking::create([
            'booking_code' => 'BNDESIGN01',
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'booking_date' => today()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'custom_design_photo' => $fakeBase64,
            'design_notes' => 'Gradasi warna nude pink dengan glitter silver',
            'total_price' => 150000,
            'dp_amount' => 45000,
            'status' => Booking::STATUS_PENDING,
            'is_walk_in' => false,
        ]);

        $this->assertTrue($booking->is_base64_design);
        $this->assertEquals($fakeBase64, $booking->custom_design_photo_url);

        // 1. Cek tampilan preview pada halaman admin bookings
        $adminView = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
        $adminView->assertStatus(200);
        $adminView->assertSee('Referensi Desain Pelanggan');
        $adminView->assertSee('Gradasi warna nude pink dengan glitter silver');
        $adminView->assertSee('Foto Tersedia (Base64)');
        $adminView->assertSee('Lihat Referensi Desain');
        $adminView->assertSee('designPreviewModal');

        // 2. Cek tampilan preview pada halaman teknisi (nail artist)
        $artistUser = $this->artist->user;
        $artistView = $this->actingAs($artistUser)->get(route('artist.dashboard'));
        $artistView->assertStatus(200);
        $artistView->assertSee('Foto Referensi Desain Tamu');
        $artistView->assertSee('Gradasi warna nude pink dengan glitter silver');
        $artistView->assertSee('Lihat Desain');
        $artistView->assertSee('designModalOpen');

        // 3. Cek tampilan pada dashboard pelanggan
        $customerView = $this->actingAs($this->customer)->get(route('dashboard'));
        $customerView->assertStatus(200);
        $customerView->assertSee('Lihat Desain');
    }
}