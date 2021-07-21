<?php

namespace Database\Factories;

use App\Models\AccountType;
use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(20),
            'active' => $this->faker->boolean(),
            'creditLimit' => $this->faker->randomNumber(),
            'bank_id' => Bank::inRandomOrder()->first()->id,
            'account_type_id' => AccountType::inRandomOrder()->first()->id,

        ];
    }
}
