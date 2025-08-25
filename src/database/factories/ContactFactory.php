<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use App\Models\Category;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {

        $category = Category::inRandomOrder()->first();
        $gender = $this->faker->numberBetween(1, 3);

        $this->faker->addProvider(new \Faker\Provider\ja_JP\Person($this->faker));
        $this->faker->addProvider(new \Faker\Provider\ja_JP\Address($this->faker));
        $this->faker->addProvider(new \Faker\Provider\ja_JP\PhoneNumber($this->faker));
        return [
            'category_id' => $category->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'gender' => $gender,
            'email' => $this->faker->unique()->safeEmail,
            'tel' => $this->faker->phoneNumber,
            'address' => $this->faker->city . $this->faker->streetAddress,
            'building' => $this->faker->secondaryAddress,
            'detail' => $this->faker->realText(50),
        ];
    }
}
