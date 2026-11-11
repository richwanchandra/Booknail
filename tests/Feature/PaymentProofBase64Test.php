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
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentProofBase64Test extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected NailArtist $artist;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'nail_artist', 'guard_name' => 'web']);

        $this->customer = User::factory()->create([
            'phone' => '081234567890',
            'loyalty_points' => 0,
        ]);
        $this->customer->assignRole('customer');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $artistUser = User::factory()->create();
        $artistUser->assignRole('nail_artist');
        $this->artist = NailArtist::create([
            'user_id' => $artistUser->id,
            'name' => 'Lily Nail Artist',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Gel Art',
            'slug' => 'gel-art',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name' => 'Classic Gel Polish',
            'category_id' => $category->id,
            'price' => 150000,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
    }

    public function test_payment_proof_stored_as_base64_via_upload_proof_route(): void
    {
        Storage::fake('public');

        // Create a PENDING booking directly (bypassing the wizard to keep test simple)
        $booking = Booking::create([
            'booking_code' => 'BN'.date('ymd').'T01',
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'total_price' => 150000,
            'dp_amount' => 45000,
            'status' => Booking::STATUS_PENDING,
            'is_walk_in' => false,
        ]);

        $fakeProof = UploadedFile::fake()->image('bukti_transfer.jpg', 400, 400);

        $response = $this->actingAs($this->customer)
            ->post(route('booking.uploadProof', $booking), [
                'dp_proof_file' => $fakeProof,
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertSessionHas('success');

        $payment = Payment::where('booking_id', $booking->id)->where('payment_type', 'dp')->first();
        $this->assertNotNull($payment);

        // Verify Base64 storage — no file on disk
        $this->assertNotNull($payment->proof_file);
        $this->assertStringStartsWith('data:image/', $payment->proof_file);
        $this->assertStringContainsString(';base64,', $payment->proof_file);
        $this->assertTrue($payment->is_base64_proof);

        // proof_url should return the data URI directly
        $this->assertEquals($payment->proof_file, $payment->proof_url);

        // proof_display_name contains booking code
        $this->assertStringContainsString($booking->booking_code, $payment->proof_display_name);

        // Verify NO payment files written to public storage
        $this->assertEmpty(Storage::disk('public')->allFiles('payments'));
    }

    public function test_upload_payment_proof_updates_base64_and_removes_old_physical_file(): void
    {
        Storage::fake('public');

        // Create booking with an old physical file
        $oldFilePath = 'payments/old_proof.jpg';
        Storage::disk('public')->put($oldFilePath, 'fake content');

        $booking = Booking::create([
            'booking_code' => 'BN'.date('ymd').'999',
            'customer_id' => $this->customer->id,
            'artist_id' => $this->artist->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
            'total_price' => 150000,
            'dp_amount' => 45000,
            'status' => Booking::STATUS_PENDING,
            'is_walk_in' => false,
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'dp',
            'proof_file' => $oldFilePath,
            'amount' => 45000,
            'status' => Payment::STATUS_PENDING,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($oldFilePath));

        // Upload new payment proof
        $newProof = UploadedFile::fake()->image('new_proof.png', 500, 500);

        $response = $this->actingAs($this->customer)
            ->post(route('booking.uploadProof', $booking), [
                'dp_proof_file' => $newProof,
                'payment_method' => 'qris',
            ]);

        $response->assertSessionHas('success');

        $payment->refresh();

        // Proof is now Base64
        $this->assertStringStartsWith('data:image/', $payment->proof_file);
        $this->assertStringContainsString(';base64,', $payment->proof_file);
        $this->assertTrue($payment->is_base64_proof);

        // Old file in public storage should be deleted
        $this->assertFalse(Storage::disk('public')->exists($oldFilePath));
        $this->assertEmpty(Storage::disk('public')->allFiles('payments'));
    }
}
