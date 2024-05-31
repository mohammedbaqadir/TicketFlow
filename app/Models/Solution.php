<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    class Solution extends Model implements HasMedia
    {
        use InteractsWithMedia;

        protected $fillable = [ 'ticket_id', 'user_id', 'content', 'resolved' ];

        public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'solution_attachments' )
                ->multipleFiles()
                ->useDisk( 'public' );
        }

        public function ticket()
        {
            return $this->belongsTo( Ticket::class );
        }

        public function user()
        {
            return $this->belongsTo( User::class );
        }


    }