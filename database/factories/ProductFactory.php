<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Curated pool of realistic car-part products keyed by name.
     *
     * @var array<string, string>
     */
    protected static array $catalog = [
        'Aki GS Astra NS40 12V 35Ah' => 'Aki kering maintenance free untuk mobil kecil, kapasitas 12V 35Ah.',
        'Aki Yuasa N70 12V 70Ah' => 'Aki basah 12V 70Ah untuk SUV dan kendaraan diesel.',
        'Aki Mobil Panasonic N50Z 12V 50Ah' => 'Aki mobil 12V 50Ah dengan teknologi grid tahan getar.',
        'Filter Oli Sakura C-1205' => 'Filter oli spin-on untuk mesin bensin 1300–1500cc.',
        'Filter Oli Denso 115010-5021' => 'Filter oli orisinal untuk Toyota Avanza dan Xenia.',
        'Filter Oli WIX 51348' => 'Filter oli high-performance untuk mesin bensin modern.',
        'Filter Solar Sakura FS-2012' => 'Filter solar untuk mesin diesel common rail.',
        'Filter Solar Baldwin BF7587' => 'Filter solar heavy-duty untuk truk dan bus.',
        'Filter Solar Fleetguard FF42000' => 'Filter solar dengan media nano untuk diesel modern.',
        'Filter Udara Sakura A-3611' => 'Filter udara panel untuk mobil MPV keluarga.',
        'Filter Udara Denso 143-2088' => 'Filter udara orisinal untuk Toyota Innova.',
        'Busi NGK BPR6ES' => 'Busi standar nickel untuk mesin bensin 4-tak.',
        'Busi Iridium NGK BKR6EIX' => 'Busi iridium performa tinggi dengan umur pakai lebih lama.',
        'Busi Denso W20EPR-U' => 'Busi standard U-groove untuk mesin bensin umum.',
        'Oli Mesin Pertamina Fastron 10W-40 1L' => 'Oli mesin semi-sintetik SAE 10W-40 untuk mesin bensin.',
        'Oli Mesin Castrol Magnatec 10W-40 1L' => 'Oli mesin semi-sintetik dengan perlindungan ekstra saat start dingin.',
        'Oli Mesin Shell Helix HX7 5W-30 1L' => 'Oli mesin sintetik SAE 5W-30 untuk mesin bensin modern.',
        'Kampas Rem Depan Akebono PS976' => 'Kampas rem depan ceramic untuk city car, rendah debu.',
        'Kampas Rem Belakang Bosch 0986AB1185' => 'Kampas rem belakang dengan teknologi noise reduksi.',
        'Ban Bridgestone Techno Sport 185/70 R14' => 'Ban radial tubeless untuk mobil keluarga, tapak awet.',
        'Ban GT Radial Champiro 165/80 R13' => 'Ban radial ekonomis untuk city car.',
        'Ban Dunlop SP Touring R1 195/65 R15' => 'Ban touring nyaman dengan daya cengkeram basah yang baik.',
        'Shockbreaker Depan KYB Excel-G 333012' => 'Shockbreaker depan twin tube untuk sedan.',
        'Shockbreaker Belakang Monroe 376001SP' => 'Shockbreaker belakang gas untuk kenyamanan berkendara.',
        'Radiator Denso Avanza/Xenia 1300cc' => 'Radiator pendingin mesin untuk MPV 1300cc.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::$catalog));

        return [
            'name' => $name,
            'description' => self::$catalog[$name],
            'price' => fake()->numberBetween(25_000, 1_500_000),
            'stock' => fake()->numberBetween(0, 50),
        ];
    }
}
