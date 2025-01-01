<?php
    declare( strict_types = 1 );

    namespace Database\Seeders;

    use App\Models\User;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\Log;

    class UserSeeder extends Seeder
    {
        private const DELAY_SECONDS = 5;

        /**
         * Run the database seeds.
         */
        public function run() : void
        {
            Log::info( 'Starting user seeding process' );

            $count = 15;
            $created = 0;

            while ( $created < $count ) {
                if ( $created > 0 ) {
                    sleep( self::DELAY_SECONDS );
                }

                User::factory()->create();
                $created++;

                Log::info( "Created user {$created} of {$count}" );
            }

            Log::info( 'User seeding completed' );
        }
    }