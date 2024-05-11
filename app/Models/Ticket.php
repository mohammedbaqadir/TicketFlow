<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Ticket extends Model
    {

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

        public function attachments()
        {
            return $this->hasMany( Attachment::class);
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
         * Get the events for the ticket.
         */
        public function events() : HasMany
        {
            return $this->hasMany( Event::class );
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