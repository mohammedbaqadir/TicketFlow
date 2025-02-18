<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use App\Config\TicketConfig;
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

    /**
     * @property int $id
     * @property string $title
     * @property string $description
     * @property int $meeting_room
     * @property-read User $requestor
     * @property-read User|null $assignee
     */
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

        /**
         * @return BelongsTo<User, Ticket>
         */
        public function requestor() : BelongsTo
        {
            return $this->belongsTo( User::class, 'requestor_id' );
        }

        /**
         * @return BelongsTo<User, Ticket>
         */
        public function assignee() : BelongsTo
        {
            return $this->belongsTo( User::class, 'assignee_id' );
        }

        /**
         * @return HasMany<Answer, Ticket>
         */
        public function answers() : HasMany
        {
            return $this->hasMany( Answer::class );
        }

        /**
         * @return HasOne<Answer, Ticket>
         */
        public function acceptedAnswer() : HasOne
        {
            return $this->hasOne( Answer::class, 'id', 'accepted_answer_id' );
        }

        // Accessors & Mutators
        public function getFormattedStatusAttribute() : string
        {
            return TicketConfig::getStatusLabel( $this->status );
        }

        public function getFormattedPriorityAttribute() : string
        {
            return TicketConfig::getPriorityLabel( $this->priority );
        }

        /**
         * @param  Builder<Ticket>  $query
         * @return Builder<Ticket>
         */
        public function scopeWithRelations( Builder $query ) : Builder
        {
            return $query->with( [
                'requestor:id,name',
                'assignee:id,name',
            ] );
        }

        /**
         * @param  Builder<Ticket>  $query
         * @return Builder<Ticket>
         */

        public function scopeUnresolved( Builder $query ) : Builder
        {
            return $query->where( 'status', '!=', 'resolved' );
        }

        // Activity Log
        public function getActivitylogOptions() : LogOptions
        {
            return LogOptions::defaults()->useLogName( 'ticket' );
        }

        /**
         * Get the indexable data array for the model.
         *
         * @return array<string, mixed>
         */
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