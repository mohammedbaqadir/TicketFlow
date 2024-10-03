<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use App\Observers\TicketObserver;
    use Illuminate\Database\Eloquent\Attributes\ObservedBy;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
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
        use HasFactory;

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

        // Accessors & Mutators
        public function getFormattedStatusAttribute() : string
        {
            return config( "enums.ticket_status.{$this->status}" );
        }

        public function getFormattedPriorityAttribute() : string
        {
            return config( "enums.ticket_priority.{$this->priority}" );
        }


        public function scopeWithRelations( Builder $query ) : Builder
        {
            return $query->with( [
                'requestor:id,name',
                'assignee:id,name',
            ] );
        }

        public function scopeUnresolved( Builder $query ) : Builder
        {
            return $query->whereNot( 'status', '!=', 'resolved' );
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
                'assignee_id' => $this->assignee_id,
            ];
        }

        /**
         * Configure Scout settings for the Meilisearch index.
         *
         * @return array
         */
        #[SearchUsingSettings]
        public function searchableSettings() : array
        {
            return [
                'filterableAttributes' => [
                    'requestor_id',  // Make requestor_id filterable
                    'assignee_id',   // Make assignee_id filterable
                ],
            ];
        }

        /**
         * Generate an excerpt of the ticket's content.
         *
         * This method creates a short excerpt from the ticket's title and description,
         * centered around the first occurrence of the search query if provided.
         *
         * @param  string|null  $query  The search query to highlight in the excerpt
         * @return string The generated excerpt
         */
        public function generateExcerpt( ?string $query = null ) : string
        {
            // Combine title and description for full content
            $content = $this->title . ' ' . $this->description;
            $excerptLength = 100;
            $start = 0;
            $excerpt = '';

            if ( $query !== null ) {
                // Find the position of the query in the content
                $position = stripos( $content, $query );
                if ( $position !== false ) {
                    // Calculate the start position for the excerpt
                    // Start 50 characters before the query, or at the beginning if that's not possible
                    $start = max( 0, $position - 50 );
                }
            }

            // Extract the excerpt
            $excerpt = substr( $content, $start, $excerptLength );

            // Add ellipsis at the start if the excerpt doesn't start at the beginning of the content
            if ( $start > 0 ) {
                $excerpt = '...' . $excerpt;
            }

            // Add ellipsis at the end if the content continues after the excerpt
            if ( \strlen( $content ) > $start + $excerptLength ) {
                $excerpt .= '...';
            }

            return $excerpt;
        }


    }