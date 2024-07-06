<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Comment extends Model
    {
        use SoftDeletes;

        protected $fillable = [ 'content', 'user_id', 'commentable_id', 'commentable_type' ];

        protected $casts = [
            'created_at' => 'datetime',
        ];

        public function commenter() : BelongsTo
        {
            return $this->belongsTo( User::class, 'user_id' );
        }

        public function commentable() : MorphTo
        {
            return $this->morphTo();
        }

        public function getFormattedCreationDateAttribute() : string
        {
            return $this->created_at->format( 'F j, Y, g:i a' );
        }

    }