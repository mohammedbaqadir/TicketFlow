<?php
    declare( strict_types = 1 );

    namespace Database\Seeders;

    use App\Models\User;
    use Illuminate\Database\Seeder;

    class UserSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run() : void
        {
            User::factory()->create( [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt( 'password' ),
                'role' => 'admin'
            ] );

            User::factory( 15 )->create();
        }
    }