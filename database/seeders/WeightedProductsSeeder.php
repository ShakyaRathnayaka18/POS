<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightedProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get Grocery Items category
        $category = Category::firstOrCreate(
            ['cat_name' => 'Grocery Items'],
            ['description' => 'Weighted grocery items sold by weight']
        );

        $products = [
            ['code' => 878, 'name' => 'සුදු කැකුලු / White rice'],
            ['code' => 879, 'name' => 'රතු කැකුලු / Red rice'],
            ['code' => 880, 'name' => 'සුදු නාඩු හාල් / White rice'],
            ['code' => 881, 'name' => 'රෝස හාල් / Pink rice'],
            ['code' => 882, 'name' => 'කෑලි හාල් / Broken rice'],
            ['code' => 883, 'name' => 'තම්බපු කීරි සම්බ / Boiled keeri samba'],
            ['code' => 884, 'name' => 'රට නාඩු / Rata nadu'],
            ['code' => 885, 'name' => 'සම්බ සහල් / Samba rice'],
            ['code' => 886, 'name' => 'රතු සම්බ / Red samba'],
            ['code' => 887, 'name' => 'සුදු සම්බ / White samba'],
            ['code' => 888, 'name' => 'තම්බපු සහල් / Boiled rice'],
            ['code' => 889, 'name' => 'තම්බපු සම්බා / Boiled samba'],
            ['code' => 890, 'name' => 'බාස්මතී / Basmati'],
            ['code' => 891, 'name' => 'ගමේ නාඩු / Game nadu'],
            ['code' => 892, 'name' => 'කැඩුනු සහල් / Broken rice'],
            ['code' => 893, 'name' => 'කීරි සුදු කැකුලු / Keeri white rice'],
            ['code' => 894, 'name' => 'කීරි රතු කැකුලු / Keeri red rice'],
            ['code' => 895, 'name' => 'කීරි රතු සම්බ / Keeri red samba'],
            ['code' => 896, 'name' => 'රතු සීනි / Red sugar'],
            ['code' => 897, 'name' => 'සුදු සීනි / White sugar'],
            ['code' => 898, 'name' => 'තේ කොල / Tea leaves'],
            ['code' => 899, 'name' => 'පරිප්පු 1 / Dhal 1'],
            ['code' => 900, 'name' => 'පරිප්පු 2 / Dhal 2'],
            ['code' => 901, 'name' => 'කප් පරිප්පු / Cup dal'],
            ['code' => 902, 'name' => 'කඩල පරිප්පු / Chickpeas dal'],
            ['code' => 903, 'name' => 'කඩල J / Chickpeas J'],
            ['code' => 904, 'name' => 'කඩල M / Chickpeas M'],
            ['code' => 905, 'name' => 'මුං ඇට / Green beans'],
            ['code' => 906, 'name' => 'සුදු කව්පි / White peas'],
            ['code' => 907, 'name' => 'රතු කව්පි / Red peas'],
            ['code' => 908, 'name' => 'කොල්ලු / Kollu'],
            ['code' => 909, 'name' => 'ලංකා අල / Lanka potatoes'],
            ['code' => 910, 'name' => 'රට අල .1 / Country potatoes .1'],
            ['code' => 911, 'name' => 'රට අල .2 / Country potatoes .2'],
            ['code' => 912, 'name' => 'බී ළුෑණු රට / Onion country'],
            ['code' => 913, 'name' => 'බී ළුෑණු ලංකා / Onion Sri Lanka'],
            ['code' => 914, 'name' => 'රතු ළුෑණු / Red onion'],
            ['code' => 915, 'name' => 'සුදු ළුෑණු / White onion'],
            ['code' => 916, 'name' => 'පාන්පිටි / Bread flour'],
            ['code' => 917, 'name' => 'ඉදි ආප්ප පිටි / Idi hopper flour'],
            ['code' => 918, 'name' => 'උම්බලකඩ ලොකු / Large mackerel'],
            ['code' => 919, 'name' => 'උම්බලකඩ කෑලි / Pieces of mackerel'],
            ['code' => 920, 'name' => 'කුරක්කන් පිටි / Kurakkan flour'],
            ['code' => 921, 'name' => 'ඉරිගු පිටි / Corn flour'],
            ['code' => 922, 'name' => 'නූඩල්ස් / Noodles'],
            ['code' => 923, 'name' => 'කොත්තමල්ලි / Coriander'],
            ['code' => 924, 'name' => 'සූදුරු / Cloves'],
            ['code' => 925, 'name' => 'මාදුරු / Maduru'],
            ['code' => 926, 'name' => 'කුරුදු / Cinnamon'],
            ['code' => 927, 'name' => 'එනසාල් / Enasal'],
            ['code' => 928, 'name' => 'ගොරකා / Goraka'],
            ['code' => 929, 'name' => 'ගොරකා කප් / Goraka caps'],
            ['code' => 930, 'name' => 'මිරිස් කරල් / Chili pods'],
            ['code' => 931, 'name' => 'ඉගුරු / Iguru'],
            ['code' => 932, 'name' => 'සියබලා / Siyabala'],
            ['code' => 933, 'name' => 'අමු කහ / Amu Kaha'],
            ['code' => 934, 'name' => 'මිරිස් කුඩු / Chili powder'],
            ['code' => 935, 'name' => 'තුනපහ කුඩු / Tunapaha powder'],
            ['code' => 936, 'name' => 'බැදපු තුනපහ / Roasted Tunapaha pieces'],
            ['code' => 937, 'name' => 'කෑලි මිරිස් / Chili powder'],
            ['code' => 938, 'name' => 'කහ කුඩු / Yellow powder'],
            ['code' => 939, 'name' => 'ගම්මිරිස් ඇට / Gummy seeds'],
            ['code' => 940, 'name' => 'ගම්මිරිස් කුඩු / Gummy powder'],
            ['code' => 941, 'name' => 'සව් හාල් / Saw rice'],
            ['code' => 942, 'name' => 'පපඩම් / Papadam'],
            ['code' => 943, 'name' => 'අබ / Aba'],
            ['code' => 944, 'name' => 'උළුහාල් / Ulu hal'],
            ['code' => 945, 'name' => 'ඊස්ට් / East'],
            ['code' => 946, 'name' => 'බල කරවල / Bala karawala'],
            ['code' => 947, 'name' => 'හුරුල්ලෝ කරවල / Hurullo karawala'],
            ['code' => 948, 'name' => 'කුකුලා කරවල / Kukula karawala'],
            ['code' => 949, 'name' => 'අංගුළු කරවල / Angulu karawala'],
            ['code' => 950, 'name' => 'රට හාල්මැස්සො / Rata halmasso'],
            ['code' => 951, 'name' => 'ලංක හාල්මැස්සො / Lanka halmasso'],
            ['code' => 952, 'name' => 'කලු කරවල / Kalu karawala'],
            ['code' => 953, 'name' => 'පියමැස්සා / Piyamassa'],
            ['code' => 954, 'name' => 'කෙන්ද / Kenda'],
            ['code' => 955, 'name' => 'බොම්බිලි / Bombili'],
            ['code' => 956, 'name' => 'කූනිස්ස / Kunisso'],
            ['code' => 957, 'name' => 'ඉස්සා / Issa'],
            ['code' => 958, 'name' => 'ලින්නා බෝල්ලු / Linna bollu'],
            ['code' => 959, 'name' => 'බෝල්ලු කරවල / Bollu karawala'],
            ['code' => 960, 'name' => 'ලේනා පරව් / Lena parau'],
            ['code' => 961, 'name' => 'කෙලවල්ලා කූරි / Kelawalla kuri'],
            ['code' => 962, 'name' => 'කට්ට කරවල / Katta karawala'],
            ['code' => 963, 'name' => 'කීරමින් කරවල / Keeramin karawala'],
            ['code' => 964, 'name' => 'පලපු ලින්නා / Palapu linna'],
            ['code' => 965, 'name' => 'වන්න පරව් / Wanna parau'],
            ['code' => 966, 'name' => 'කොරලි කරවල / Korali Karawala'],
            ['code' => 967, 'name' => 'දුම් කරවල / Dhum karawala'],
            ['code' => 968, 'name' => 'මඩු කරවල / Madu karawala'],
            ['code' => 969, 'name' => 'බෝට්ටු බලය / Bottu balaya'],
            ['code' => 970, 'name' => 'වියලි මිදි / Dried grapes'],
            ['code' => 971, 'name' => 'රට ඉදි / Rata Indi'],
            ['code' => 972, 'name' => 'අජිනමොටෝ / Ajinamoto'],
            ['code' => 973, 'name' => 'ආප්ප සෝඩා / App soda'],
            ['code' => 974, 'name' => 'සෝයා මීට් / Soya meat'],
            ['code' => 975, 'name' => 'පොල් තෙල් 1 / Coconut oil 1'],
            ['code' => 976, 'name' => 'පොල් තෙල් 2 / Coconut oil 2'],
            ['code' => 977, 'name' => 'ෆාම් ඔයිල් / Farm oil'],
            ['code' => 978, 'name' => 'පොල් තෙල් RBD / Coconut oil RBD'],
            ['code' => 979, 'name' => 'කුකුල් මස් / Chicken'],
            ['code' => 980, 'name' => 'potatto'],
        ];

        $imported = 0;
        $skipped = 0;

        foreach ($products as $product) {
            $weightedCode = str_pad($product['code'], 6, '0', STR_PAD_LEFT);

            // Skip if already exists
            if (Product::where('weighted_product_code', $weightedCode)->exists()) {
                $skipped++;
                continue;
            }

            // Create product - SKU will be auto-generated by Product model
            Product::create([
                'product_name' => $product['name'],
                'weighted_product_code' => $weightedCode,
                'is_weighted' => true,
                'pricing_type' => 'per_kg',
                'allow_decimal_sales' => true,
                'unit' => 'kg',
                'base_unit' => 'g',
                'conversion_factor' => 1000,
                'category_id' => $category->id,
                'brand_id' => null,
                'minimum_stock' => 0,
                'initial_stock' => 0,
                'description' => 'Weighted product - sold by weight',
            ]);

            $imported++;
        }

        $this->command->info("Imported: {$imported} products, Skipped: {$skipped} duplicates");
    }
}
