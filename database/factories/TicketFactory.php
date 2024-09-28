<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
     */
    class TicketFactory extends Factory
    {
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
                'status' => $this->faker->randomElement( [
                    'open', 'in-progress', 'awaiting-acceptance', 'escalated', 'resolved'
                ] ),
                'priority' => $this->faker->randomElement( [ 'low', 'medium', 'high' ] ),
                'timeout_at' => $this->faker->dateTimeBetween( '-2 hours', '+2 hours' ),
                'requestor_id' => \App\Models\User::factory(),
                'assignee_id' => \App\Models\User::factory(),
                'meeting_room' => $this->faker->optional()->word(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        public function timedOut()
        {
            return $this->state( [
                'timeout_at' => now()->subMinutes( 10 ), // Simulate timed-out tickets
            ] );
        }

        public function notTimedOut()
        {
            return $this->state( [
                'timeout_at' => now()->addMinutes( 10 ), // Simulate future timeouts
            ] );
        }
    }