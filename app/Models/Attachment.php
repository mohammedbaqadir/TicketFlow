<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class Attachment extends Model
    {

        protected $fillable = [ 'ticket_id', 'user_id', 'solution_id', 'file_path' ];

        /**
         * Get the ticket that owns the attachment.
         */
        public function ticket() : BelongsTo
        {
            return $this->belongsTo( Ticket::class );
        }

        public function solution()
        {
            return $this->belongsTo( Solution::class );
        }

        /**
         * Get the user that owns the attachment.
         */
        public function user() : BelongsTo
        {
            return $this->belongsTo( User::class );
        }

    }