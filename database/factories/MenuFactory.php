<?php

namespace Database\Factories;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Eastern_food', 'Western_food', 'Desserts', 'Juices'];
        $imageName = 'menu_' . fake()->unique()->numberBetween(1, 100) . '.jpg';

        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(10), 
            'price' => fake()->randomFloat(2, 5, 50),
            'category' => fake()->randomElement($categories), 
            'image_name' => $imageName,

        ];
    }
}
