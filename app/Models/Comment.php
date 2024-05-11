<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [ 'ticket_id', 'user_id', 'content' ];

    /**
     * Get the ticket that owns the comment.
     */
    public function ticket()
    {
        return $this->belongsTo( Ticket::class );
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo( User::class );
    }

}