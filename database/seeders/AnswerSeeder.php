<?php
    declare( strict_types = 1 );

    namespace Database\Seeders;

    use App\Models\Answer;
    use Illuminate\Database\Seeder;

    class AnswerSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run() : void
        {
            Answer::factory()->count( 50 )->create()->each( function ( $answer ) {
                if ( $answer->is_accepted ) {
                    $answer->ticket()->update( [ 'accepted_answer_id' => $answer->id ] );
                }
            } );
        }
    }