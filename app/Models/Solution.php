<?php

    namespace App\Models;

    use App\Observers\SolutionObserver;
    use Illuminate\Database\Eloquent\Attributes\ObservedBy;
    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;
    use Spatie\Activitylog\LogOptions;
    use Spatie\Activitylog\Traits\LogsActivity;
    use Spatie\EloquentSortable\Sortable;
    use Spatie\EloquentSortable\SortableTrait;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    #[ObservedBy( [ SolutionObserver::class ] )]
    class Solution extends Model implements HasMedia, Sortable
    {
        use InteractsWithMedia;
        use SortableTrait;
        use LogsActivity;


        protected $fillable = [ 'ticket_id', 'user_id', 'content', 'resolved' ];


        public function ticket()
        {
            return $this->belongsTo( Ticket::class );
        }

        public function submitter()
        {
            return $this->belongsTo( User::class );
        }

        public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'solution_attachments' );
        }

        public function isSubmitter(User $user) {
            return $this->user_id === $user->id;
        }
        public function markValid()
        {
            $this->update( [ 'resolved' => true ] );

            $this->ticket->update( [ 'status' => 'closed' ] );
        }

        public function markInvalid()
        {
            $this->update( [ 'resolved' => false ] );

            $this->ticket->update( [ 'status' => 'in-progress' ] );
        }


        /**
         * @return LogOptions
         */
        public function getActivitylogOptions() : LogOptions
        {
            return LogOptions::defaults()
                ->useLogName( 'solution' );
        }
    }