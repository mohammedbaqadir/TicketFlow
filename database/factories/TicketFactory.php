<?php
    declare( strict_types = 1 );

    namespace Database\Factories;

    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Carbon;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
     */
    class TicketFactory extends Factory
    {
        protected $model = Ticket::class;

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition() : array
        {
            return [
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'status' => Arr::random( array_keys( config( 'enums.ticket_status' ) ) ),
                'priority' => Arr::random( array_keys( config( 'enums.ticket_priority' ) ) ),
                'timeout_at' => $this->faker->dateTimeBetween( '-2 hours', '+2 hours' ),
                'requestor_id' => User::isEmployee()->inRandomOrder()->first()->id,
                'assignee_id' => User::isAgent()->inRandomOrder()->first()->id,
                'meeting_room' => $this->faker->optional()->word(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

    }