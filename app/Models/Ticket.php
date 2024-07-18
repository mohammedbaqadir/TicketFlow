<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use App\Observers\TicketObserver;
    use Illuminate\Database\Eloquent\Attributes\ObservedBy;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\MorphMany;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Laravel\Scout\Searchable;
    use Spatie\Activitylog\LogOptions;
    use Spatie\Activitylog\Traits\LogsActivity;

    #[ObservedBy( [ TicketObserver::class ] )]
    class Ticket extends Model
    {
        use LogsActivity;
        use SoftDeletes;
        use Searchable;

        protected $fillable = [
            'title', 'description', 'status', 'priority', 'requestor_id', 'assignee_id', 'timeout_at',
            'accepted_answer_id', 'meeting_room'
        ];

        protected $casts = [
            'timeout_at' => 'datetime',
        ];

        // Relationships
        public function requestor() : BelongsTo
        {
            return $this->belongsTo( User::class, 'requestor_id' );
        }

        public function assignee() : BelongsTo
        {
            return $this->belongsTo( User::class, 'assignee_id' );
        }

        public function answers() : HasMany
        {
            return $this->hasMany( Answer::class );
        }

        public function acceptedAnswer() : HasOne
        {
            return $this->hasOne( Answer::class, 'id', 'accepted_answer_id' );
        }

        public function comments() : MorphMany
        {
            return $this->morphMany( Comment::class, 'commentable' );
        }

        // Accessors & Mutators
        public function getFormattedStatusAttribute() : string
        {
            return config( "enums.ticket_status.{$this->status}" );
        }

        public function getFormattedPriorityAttribute() : string
        {
            return config( "enums.ticket_priority.{$this->priority}" );
        }

        // Activity Log
        public function getActivitylogOptions() : LogOptions
        {
            return LogOptions::defaults()->useLogName( 'ticket' );
        }

        // Scout Search
        public function toSearchableArray() : array
        {
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'requestor_id' => $this->requestor_id,
            ];
        }

    }