<?php

namespace Database\Factories;

use App\Models\UserProperty;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProperty>
 */
class UserPropertyFactory extends Factory
{
    protected $model = UserProperty::class;

    public function withType(string $key): array
    {
        return match ($key) {
            'phone' => [
                'property_key' => 'phone',
                'property_value' => fake()->unique()->phoneNumber(),
            ],
            'email' => [
                'property_key' => 'email',
                'property_value' => fake()->unique()->safeEmail(),
            ],
            default => throw new Exception("Не удалось заполнить данные, тип $key не найден")
        };
    }

    /**
     * @throws Exception
     */
    public function definition(): array
    {
        return $this->withType(fake()->randomElement(['phone', 'email']));
    }
}
