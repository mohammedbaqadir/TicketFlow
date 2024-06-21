<?php

    namespace App\Models;

    use App\Enums\TicketStatus;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Spatie\Activitylog\LogOptions;
    use Spatie\Activitylog\Traits\LogsActivity;
    use Spatie\EloquentSortable\Sortable;
    use Spatie\EloquentSortable\SortableTrait;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    class Ticket extends Model implements HasMedia, Sortable
    {
        use InteractsWithMedia;
        use LogsActivity;
        use SortableTrait;

        public $sortable = [
            'order_column_name' => 'created_at',
        ];
        /**
         * Configure the activity log options for the Ticket model.
         *
         * @return LogOptions
         */
        public function getActivitylogOptions() : LogOptions
        {
            return LogOptions::defaults()
                ->useLogName( 'ticket' )
                ->logOnly( [ 'status' ] )
                ->logOnlyDirty()
                ->setDescriptionForEvent( function ( string $eventName ) {
                    return "Ticket is {$this->status}";
                } );
        }


        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'title',
            'description',
            'status',
            'priority',
            'created_by',
            'assigned_to',
            'timeout_at'
        ];



    public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'ticket_attachments' );
        }


        protected $casts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'timeout_at' => 'datetime',
        ];


        /**
         * Determine if the given user is the requestor of the ticket.
         *
         * @param  User  $user
         * @return bool
         */
        public function isRequestor( User $user ) : bool
        {
            return $this->created_by === $user->id;
        }

        /**
         * Determine if the given user is the assignee of the ticket.
         *
         * @param  User  $user
         * @return bool
         */
        public function isAssignee( User $user ) : bool
        {
            return $this->assigned_to === $user->id;
        }

        /**
         * Get the user who created the ticket.
         *
         * @return BelongsTo
         */
        public function requestor() : BelongsTo
        {
            return $this->belongsTo( User::class, 'created_by' );
        }


        /**
         * Get the comments for the ticket.
         */
        public function comments() : HasMany
        {
            return $this->hasMany( Comment::class );
        }

        public function solutions()
        {
            return $this->hasMany( Solution::class );
        }


        /**
         * Get the user to whom the ticket is assigned.
         *
         * @return BelongsTo
         */
        public function assignee() : BelongsTo
        {
            return $this->belongsTo( User::class, 'assigned_to' );
        }

        public function scopeIsOpen( $query )
        {
            return $query->where( 'status', 'open' );
        }

        public function scopeIsInProgress( $query )
        {
            return $query->where( 'status', 'in-progress' );
        }

        public function scopeIsAwaitingAcceptance( $query )
        {
            return $query->where( 'status', 'awaiting-acceptance' );
        }

        public function scopeIsElevated( $query )
        {
            return $query->where( 'status', 'elevated' );
        }

        public function scopeIsClosed( $query )
        {
            return $query->where( 'status', 'closed' );
        }


    }