<?php
    declare( strict_types = 1 );

    namespace Database\Factories;

    use App\Config\TicketConfig;
    use App\Models\Ticket;
    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Arr;

    /**
     * @extends Factory<Ticket>
     */
    class TicketFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         * @var class-string<Ticket>
         */
        protected $model = Ticket::class;

        public function definition() : array
        {
            return [
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'status' => Arr::random( TicketConfig::getStatusKeys() ),
                'priority' => Arr::random( TicketConfig::getPriorityKeys() ),
                'timeout_at' => $this->faker->dateTimeBetween( '-2 hours', '+2 hours' ),
                'requestor_id' => null,  // Optional by default
                'assignee_id' => null,   // Optional by default
                'meeting_room' => $this->faker->optional()->word(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        /**
         * Set the requestor for the ticket
         */
        public function withRequestor( ?User $user = null ) : self
        {
            return $this->state( fn( array $attributes ) => [
                'requestor_id' => $user->id ??
                    User::factory()->state( [ 'role' => 'employee' ] )->create()->id
            ] );
        }

        /**
         * Set the assignee for the ticket
         */
        public function withAssignee( ?User $user = null ) : self
        {
            return $this->state( fn( array $attributes ) => [
                'assignee_id' => $user->id ??
                    User::factory()->state( [ 'role' => 'agent' ] )->create()->id
            ] );
        }

        /**
         * Set ticket status to in-progress
         */
        public function inProgress() : self
        {
            return $this->state( [
                'status' => 'in-progress'
            ] );
        }

        /**
         * Set ticket status to open
         */
        public function open() : self
        {
            return $this->state( [
                'status' => 'open'
            ] );
        }

        /**
         * Set ticket status to resolved
         */
        public function resolved() : self
        {
            return $this->state( [
                'status' => 'resolved'
            ] );
        }

        /**
         * Create a ticket with all required relationships
         */
        public function complete() : self
        {
            return $this->withRequestor()->withAssignee();
        }
    }