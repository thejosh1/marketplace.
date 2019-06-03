<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //how many categories do you need
        $count = (int)$this->command->ask('How many products do you need? defaulting to', 10);
        $this->command->info("creating {$count} categories.");


        //create the category
        $categories = factory(App\Category::class, $count)->create();

        $this->command->info('categories created');

    }
}
