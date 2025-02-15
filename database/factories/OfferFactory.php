<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Eastern_food', 'Western_food', 'Desserts', 'Juices'];
        $imageName = 'menu_' . fake()->unique()->numberBetween(1, 100) . '.jpg';
        $oldprice = fake()->randomFloat(2, 5, 50);
        $discountPercentage = fake()->randomFloat(2, 10, 40); 
        $newprice = $oldprice * (1 - ($discountPercentage / 100));
        return [
                'name' => fake()->word(),
                'description' => fake()->sentence(10), 
                'oldprice' => $oldprice,
                'newprice' => $newprice,
                'category' => fake()->randomElement($categories), 
                'image_name' => $imageName,
    
        ];
    }
}
