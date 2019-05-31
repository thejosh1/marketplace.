<?php

use Illuminate\Database\Seeder;
use App\Product;
use phpDocumentor\Reflection\Types\Integer;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //how many products do you need defaulting to 50
        $count = (int)$this->command->ask('How many products do you need? defaulting to', 50);
        $this->command->info("creating {$count} products.");

        //create the products
        $products = factory(App\Product::class, $count)->create();

        $this->command->info('products created');
        

    }
}
