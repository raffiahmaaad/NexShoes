<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Hitam', 'Putih', 'Merah', 'Biru', 'Hijau', 'Abu-abu'];

        $size = fake()->randomElement($sizes);
        $color = fake()->randomElement($colors);

        return [
            'product_id' => Product::factory(),
            'name' => $size . ' - ' . $color,
            'sku' => fake()->unique()->bothify('VAR-####-??'),
            'price' => fake()->numberBetween(50000, 500000),
            'stock' => fake()->numberBetween(0, 100),
            'attributes' => [
                'size' => $size,
                'color' => $color,
            ],
            'image' => null,
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 100),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function withSize(string $size): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $size . ' - ' . (fake()->randomElement(['Hitam', 'Putih', 'Merah', 'Biru'])),
            'attributes' => array_merge($attributes['attributes'] ?? [], [
                'size' => $size,
            ]),
        ]);
    }

    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => (fake()->randomElement(['S', 'M', 'L', 'XL'])) . ' - ' . $color,
            'attributes' => array_merge($attributes['attributes'] ?? [], [
                'color' => $color,
            ]),
        ]);
    }

    public function withPrice(int $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }
}
