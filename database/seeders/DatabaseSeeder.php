<?php

namespace Database\Seeders;

use App\Models\ArtistSchedule;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Roles ---
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $artistRole = Role::firstOrCreate(['name' => 'artist', 'guard_name' => 'web']);

        // --- Admin User ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@booknail.id'],
            [
                'name' => 'Admin Booknail',
                'phone' => '081234567890',
                'password' => bcrypt('password123'),
                'loyalty_points' => 0,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // --- Customer Sample ---
        $customer = User::firstOrCreate(
            ['email' => 'customer@booknail.id'],
            [
                'name' => 'Aulia Putri',
                'phone' => '089876543210',
                'password' => bcrypt('password123'),
                'loyalty_points' => 250,
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole($customerRole);

        // --- Categories ---
        $categories = [
            [
                'name' => 'Manicure',
                'slug' => 'manicure',
                'icon' => 'sparkles',
                'is_active' => true,
            ],
            [
                'name' => 'Pedicure',
                'slug' => 'pedicure',
                'icon' => 'footprints',
                'is_active' => true,
            ],
            [
                'name' => 'Nail Art Custom',
                'slug' => 'nail-art-custom',
                'icon' => 'palette',
                'is_active' => true,
            ],
            [
                'name' => 'Extension',
                'slug' => 'extension',
                'icon' => 'wand-2',
                'is_active' => true,
            ],
            [
                'name' => 'Treatment',
                'slug' => 'treatment',
                'icon' => 'heart-handshake',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $catManicure = Category::where('slug', 'manicure')->first();
        $catPedicure = Category::where('slug', 'pedicure')->first();
        $catCustom = Category::where('slug', 'nail-art-custom')->first();
        $catExtension = Category::where('slug', 'extension')->first();
        $catTreatment = Category::where('slug', 'treatment')->first();

        // --- Services ---
        $services = [
            // Manicure
            ['category_id' => $catManicure->id, 'name' => 'Basic Manicure', 'price' => 85000, 'duration_minutes' => 45, 'description' => 'Perawatan dasar kuku tangan: potong, rapikan, poles, dan masker tangan.', 'is_active' => true],
            ['category_id' => $catManicure->id, 'name' => 'Gel Manicure', 'price' => 185000, 'duration_minutes' => 75, 'description' => 'Manicure dengan kutikel gel tahan lama hingga 3 minggu.', 'is_active' => true],
            ['category_id' => $catManicure->id, 'name' => 'French Manicure', 'price' => 150000, 'duration_minutes' => 60, 'description' => 'Manicure gaya klasik French dengan ujung kuku berwarna putih.', 'is_active' => true],

            // Pedicure
            ['category_id' => $catPedicure->id, 'name' => 'Basic Pedicure', 'price' => 120000, 'duration_minutes' => 60, 'description' => 'Perawatan kaki lengkap: rendam air hangat, scrub, masker kaki.', 'is_active' => true],
            ['category_id' => $catPedicure->id, 'name' => 'Spa Pedicure Deluxe', 'price' => 220000, 'duration_minutes' => 90, 'description' => 'Pedicure spa premium dengan pijat refleksi kaki 30 menit.', 'is_active' => true],
            ['category_id' => $catPedicure->id, 'name' => 'Gel Pedicure', 'price' => 230000, 'duration_minutes' => 90, 'description' => 'Pedicure lengkap dengan aplikasi kutikel gel pada kuku kaki.', 'is_active' => true],

            // Nail Art Custom
            ['category_id' => $catCustom->id, 'name' => 'Nail Art Simple', 'price' => 50000, 'duration_minutes' => 30, 'description' => 'Desain kuku sederhana: dot, line, glitter, atau stiker kuku.', 'is_active' => true],
            ['category_id' => $catCustom->id, 'name' => 'Nail Art Character', 'price' => 150000, 'duration_minutes' => 60, 'description' => 'Desain karakter kartun, anime, atau motif lucu sesuai request.', 'is_active' => true],
            ['category_id' => $catCustom->id, 'name' => 'Nail Art Bridal', 'price' => 350000, 'duration_minutes' => 120, 'description' => 'Desain kuku pengantin mewah dengan rhinestone & ornamen premium.', 'is_active' => true],

            // Extension
            ['category_id' => $catExtension->id, 'name' => 'Acrylic Extension', 'price' => 350000, 'duration_minutes' => 120, 'description' => 'Extension kuku dengan bahan acrylic kokoh untuk kuku pendek.', 'is_active' => true],
            ['category_id' => $catExtension->id, 'name' => 'Soft Gel Extension', 'price' => 420000, 'duration_minutes' => 150, 'description' => 'Extension dengan soft gel yang fleksibel dan natural di kuku.', 'is_active' => true],
            ['category_id' => $catExtension->id, 'name' => 'Refill Extension', 'price' => 220000, 'duration_minutes' => 90, 'description' => 'Isi ulang bagian pertumbuhan kuku pada extension lama.', 'is_active' => true],

            // Treatment
            ['category_id' => $catTreatment->id, 'name' => 'Hand & Foot Spa', 'price' => 180000, 'duration_minutes' => 60, 'description' => 'Spa tangan dan kaki lengkap dengan scrub, masker, dan pijat.', 'is_active' => true],
            ['category_id' => $catTreatment->id, 'name' => 'Paraffin Wax Treatment', 'price' => 95000, 'duration_minutes' => 30, 'description' => 'Perawatan dengan lilin parafin untuk melembapkan kulit tangan/kaki.', 'is_active' => true],
            ['category_id' => $catTreatment->id, 'name' => 'Nail Repair & Remove', 'price' => 75000, 'duration_minutes' => 30, 'description' => 'Perbaikan kuku rusak atau penghapusan kutikel gel/extension dengan aman.', 'is_active' => true],
        ];

        foreach ($services as $svc) {
            Service::firstOrCreate(
                ['category_id' => $svc['category_id'], 'name' => $svc['name']],
                $svc
            );
        }

        // --- Nail Artists ---
        $artistUsers = [
            [
                'name' => 'Nadhira Azzahra',
                'email' => 'nadhira@booknail.id',
                'phone' => '081211112222',
                'password' => bcrypt('password123'),
                'loyalty_points' => 0,
                'email_verified_at' => now(),
                'role' => 'admin',
                'bio' => 'Senior Nail Artist dengan pengalaman 5+ tahun spesialis bridal & nail art karakter.',
                'photo' => null,
            ],
            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti@booknail.id',
                'phone' => '081233334444',
                'password' => bcrypt('password123'),
                'loyalty_points' => 0,
                'email_verified_at' => now(),
                'role' => 'customer',
                'bio' => 'Spesialis gel manicure & pedicure spa dengan sentuhan pijat refleksi.',
                'photo' => null,
            ],
            [
                'name' => 'Maya Permata',
                'email' => 'maya@booknail.id',
                'phone' => '081255556666',
                'password' => bcrypt('password123'),
                'loyalty_points' => 0,
                'email_verified_at' => now(),
                'role' => 'customer',
                'bio' => 'Ahli extension acrylic & soft gel, suka dengan desain unik dan kreatif.',
                'photo' => null,
            ],
        ];

        foreach ($artistUsers as $au) {
            $user = User::firstOrCreate(
                ['email' => $au['email']],
                [
                    'name' => $au['name'],
                    'phone' => $au['phone'],
                    'password' => $au['password'],
                    'loyalty_points' => $au['loyalty_points'],
                    'email_verified_at' => $au['email_verified_at'],
                ]
            );
            if ($au['role'] === 'admin') {
                $user->assignRole($adminRole);
            }
            $user->assignRole($artistRole);

            NailArtist::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $au['bio'],
                    'photo' => $au['photo'],
                    'is_active' => true,
                ]
            );
        }

        // --- Artist Schedules (Default: Senin - Sabtu 09:00 - 18:00, Minggu libur) ---
        $artists = NailArtist::all();
        foreach ($artists as $artist) {
            // 0 = Minggu (libur), 1-6 = Senin-Sabtu
            for ($day = 0; $day <= 6; $day++) {
                ArtistSchedule::firstOrCreate(
                    ['artist_id' => $artist->id, 'day_of_week' => $day],
                    [
                        'start_time' => $day === 0 ? '00:00:00' : '09:00:00',
                        'end_time' => $day === 0 ? '00:00:00' : '18:00:00',
                        'is_off' => $day === 0,
                    ]
                );
            }
        }
    }
}
