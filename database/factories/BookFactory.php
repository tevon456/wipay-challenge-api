<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
use App\Models\Book;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{

    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create();

        return [
            'author_name' => $faker->name,
            'title' => $faker->sentence(6),
            'isbn' => $faker->unique()->isbn13,
            'price' => $faker->randomFloat(2, 1000, 6000),
            'inventory_count' => $faker->numberBetween(0, 50),
        ];
    }
}
