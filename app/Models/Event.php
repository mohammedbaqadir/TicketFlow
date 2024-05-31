<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Event extends Model
    {
        protected $fillable = [ 'ticket_id', 'user_id', 'description' ];

        /**
         * Get the ticket that owns the event.
         */
        public function ticket()
        {
            return $this->belongsTo( Ticket::class );
        }

        /**
         * Get the user that performed the event.
         */
        public function user()
        {
            return $this->belongsTo( User::class );
        }
    }