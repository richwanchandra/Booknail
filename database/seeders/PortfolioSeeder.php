<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        if (Portfolio::count() > 0) {
            return;
        }

        $catCustom = Category::where('slug', 'nail-art-custom')->first() ?? Category::first();
        $catManicure = Category::where('slug', 'manicure')->first() ?? Category::first();
        $catPedicure = Category::where('slug', 'pedicure')->first() ?? Category::first();
        $catExtension = Category::where('slug', 'extension')->first() ?? Category::first();

        $artist1 = NailArtist::first();
        $artist2 = NailArtist::skip(1)->first() ?? $artist1;

        $service1 = Service::where('name', 'like', '%Nail Art%')->first() ?? Service::first();
        $service2 = Service::where('name', 'like', '%French%')->first() ?? Service::first();
        $service3 = Service::where('name', 'like', '%Extension%')->first() ?? Service::first();

        $items = [
            [
                'title' => 'Rose Gold French Tip Luxury with Rhinestones',
                'category_id' => $catCustom?->id,
                'service_id' => $service1?->id,
                'nail_artist_id' => $artist1?->id,
                'image' => 'https://images.unsplash.com/photo-1632345031435-8727f6897d53?auto=format&fit=crop&w=800&q=80',
                'description' => 'Desain kuku french tips anggun dengan sentuhan rose gold chrome dan hiasan kristal swarovski di aksen jemari manis.',
                'tags' => 'French Tip, Rose Gold, Luxury, Swarovski, Bridal',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Bridal Pearl & 3D Ivory White Flowers',
                'category_id' => $catCustom?->id,
                'service_id' => $service1?->id,
                'nail_artist_id' => $artist2?->id,
                'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=800&q=80',
                'description' => 'Khusus hari pernikahan impian, ornamen 3D kelopak bunga lembut dipadukan mutiara mikro dan shimmer pearl white.',
                'tags' => 'Bridal, 3D Flowers, Pearl, Elegant, Wedding',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Minimalist Nude Marble Stone Art',
                'category_id' => $catManicure?->id,
                'service_id' => $service2?->id,
                'nail_artist_id' => $artist1?->id,
                'image' => 'https://images.unsplash.com/photo-1519014816548-bf5fe059798b?auto=format&fit=crop&w=800&q=80',
                'description' => 'Motif marmer alami berpadu dengan aksen foil emas di atas dasar nude krem yang sangat natural dan rapi untuk sehari-hari.',
                'tags' => 'Marble, Gold Foil, Nude, Daily Look, Minimalist',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Soft Gel Ombre Gradient Glossy Pink',
                'category_id' => $catExtension?->id,
                'service_id' => $service3?->id,
                'nail_artist_id' => $artist2?->id,
                'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Gradasi warna baby pink ke milky white dengan top coat glass effect berdaya tahan ekstra tinggi.',
                'tags' => 'Ombre, Glossy, Soft Gel, Clean Girl, Pink',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Futuristic Silver Chrome Metallic Mirror',
                'category_id' => $catCustom?->id,
                'service_id' => $service1?->id,
                'nail_artist_id' => $artist1?->id,
                'image' => 'https://images.unsplash.com/photo-1599940824399-b87987ceb72a?auto=format&fit=crop&w=800&q=80',
                'description' => 'Tren chrome powder silver dengan kilau cermin memukau. Memberikan statement look yang edgy dan modis.',
                'tags' => 'Chrome, Metallic, Y2K, Trendy, Statement',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Velvet Burgundy Wine Red with Golden Line',
                'category_id' => $catPedicure?->id,
                'service_id' => $service2?->id,
                'nail_artist_id' => $artist2?->id,
                'image' => 'https://images.unsplash.com/photo-1607779097040-26e80aa78e66?auto=format&fit=crop&w=800&q=80',
                'description' => 'Warna merah anggur burgundy pekat nan mewah, berpadu garis emas minimalis yang menciptakan nuansa ningrat.',
                'tags' => 'Burgundy, Deep Red, Gold Line, Royal, Classic',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($items as $item) {
            Portfolio::create($item);
        }
    }
}
