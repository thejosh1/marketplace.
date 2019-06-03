<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        //ask for for db migration refresh default is no
        if($this->command->confirm('Do you wish to refresh migration before seeding, it will clear old data ?')) {
            //call the php artisan migrate:refresh using Artisan
            $this->command->call('migrate:fresh');

            $this->command->line('database cleared');
        }
        // $this->call(UsersTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CategoryTableSeeder::class);

        $this->command->info('Database seeded');

        //reguard model
        Eloquent::reguard();
    }
}
