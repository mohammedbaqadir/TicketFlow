<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use App\Observers\AnswerObserver;
    use Illuminate\Database\Eloquent\Attributes\ObservedBy;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphMany;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Spatie\Activitylog\LogOptions;
    use Spatie\Activitylog\Traits\LogsActivity;

    #[ObservedBy( [ AnswerObserver::class ] )]
    class Answer extends Model
    {
        use LogsActivity;
        use SoftDeletes;

        protected $fillable = [ 'content', 'is_accepted', 'submitter_id', 'ticket_id' ];
        protected $casts = [ 'is_accepted' => 'boolean' ];


        public function submitter() : BelongsTo
        {
            return $this->belongsTo( User::class, 'submitter_id' );
        }

        public function ticket() : BelongsTo
        {
            return $this->belongsTo( Ticket::class );
        }


        public function scopeAccepted( $query )
        {
            return $query->where( 'is_accepted', true );
        }

        public function getActivitylogOptions() : LogOptions
        {
            return LogOptions::defaults()
                ->useLogName( 'answer' );
        }

    }