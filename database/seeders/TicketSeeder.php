<?php
    declare( strict_types = 1 );

    namespace Database\Seeders;

    use App\Models\Ticket;
    use Illuminate\Database\Seeder;

    class TicketSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run() : void
        {
            Ticket::factory( 30 )->create();
        }
    }