<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Spatie\Activitylog\LogOptions;
    use Spatie\Activitylog\Traits\LogsActivity;
    use Spatie\EloquentSortable\Sortable;
    use Spatie\EloquentSortable\SortableTrait;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    class Solution extends Model implements HasMedia, Sortable
    {
        use InteractsWithMedia;
        use SortableTrait;

        protected $fillable = [ 'ticket_id', 'user_id', 'content', 'resolved' ];


        public function ticket()
        {
            return $this->belongsTo( Ticket::class );
        }

        public function user()
        {
            return $this->belongsTo( User::class );
        }

        public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'solution_attachments' );
        }

        public function markValid()
        {
            $this->update( [ 'resolved' => true ] );
            activity()
                ->on( $this->ticket )
                ->by( $this->ticket->requestor )
                ->log( 'Solution resolved the ticket' );
            $this->ticket->update( [ 'status' => 'closed' ] );
        }

        public function markInvalid()
        {
            $this->update( [ 'resolved' => false ] );
            activity()
                ->on( $this->ticket )
                ->by( $this->ticket->requestor )
                ->log( "Solution didn't resolve the ticket" );
            $this->ticket->update( [ 'status' => 'in-progress' ] );
        }


    }