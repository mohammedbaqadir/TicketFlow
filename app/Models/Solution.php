<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    protected $fillable = [ 'ticket_id', 'user_id', 'content', 'resolved' ];

    public function ticket()
    {
        return $this->belongsTo( Ticket::class );
    }

    public function user()
    {
        return $this->belongsTo( User::class );
    }

    public function attachments()
    {
        return $this->hasMany( Attachment::class );
    }
}