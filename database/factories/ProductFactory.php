<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Cincin', 'Gelang', 'Kalung', 'Anting', 'Liontin'];
        $purities = ['18K', '22K', '24K'];
        $type = $this->faker->randomElement($types);
        $name = $type . ' Emas ' . $this->faker->word();
        
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'sku' => strtoupper($this->faker->unique()->bothify('###-???-####')),
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->bothify('##??')),
            'description' => $this->faker->sentence(10),
            'gold_purity' => $this->faker->randomElement($purities),
            'weight_gram' => $this->faker->randomFloat(2, 1, 20), // 1g to 20g
            'base_price' => $this->faker->randomFloat(2, 800000, 15000000), // 800k - 15m IDR
            'buy_back_price' => $this->faker->randomFloat(2, 700000, 14000000),
            'stock' => $this->faker->numberBetween(0, 10),
            'images' => ['https://placehold.co/600x400?text=' . urlencode($type)],
            'is_available' => $this->faker->boolean(80),
            'is_reservable' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
