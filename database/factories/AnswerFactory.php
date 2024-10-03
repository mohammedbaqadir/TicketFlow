<?php

    namespace Database\Factories;

    use App\Models\Answer;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
     */
    class AnswerFactory extends Factory
    {
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition() : array
        {
            $ticket = Ticket::Unresolved()->inRandomOrder()->first();

            $isAccepted = $ticket->answers()->count() > 2 && $this->faker->boolean();

            return [
                'content' => $this->faker->paragraph,
                'submitter_id' => User::isAgent()->inRandomOrder()->first()->id,
                'ticket_id' => $ticket->id,
                'is_accepted' => $isAccepted,
            ];
        }
    }