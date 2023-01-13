<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pizzas')->insert([
            'name' => 'Original',
            'toppings' => serialize(['Cheese', 'Tomato Sauce']),
            'smallPrice' => 8,
            'mediumPrice' => 9,
            'largePrice' => 11
        ]);

        DB::table('pizzas')->insert([
            'name' => 'Gimme the Meat',
            'toppings' => serialize(['Pepperoni', 'Ham', 'Chicken', 'Minced Beef', 'Sausage', 'Bacon']),
            'smallPrice' => 11,
            'mediumPrice' => 14.50,
            'largePrice' => 16.50
        ]);

        DB::table('pizzas')->insert([
            'name' => 'Veggie Delight',
            'toppings' => serialize(['Onions', 'Green Peppers', 'Mushrooms', 'Sweetcorn']),
            'smallPrice' => 10,
            'mediumPrice' => 13,
            'largePrice' => 15
        ]);

        DB::table('pizzas')->insert([
            'name' => 'Make Mine Hot',
            'toppings' => serialize(['Chicken', 'Onions', 'Green Peppers', 'Jalapeno Peppers']),
            'smallPrice' => 11,
            'mediumPrice' =>13,
            'largePrice' => 15
        ]);
    }
}
