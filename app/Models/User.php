<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    class User extends Authenticatable implements HasMedia
    {
        use InteractsWithMedia;
        use SoftDeletes;

        protected $fillable = [ 'name', 'email', 'password', 'role', 'preferences' ];

        protected $hidden = [ 'password', 'remember_token' ];

        protected $casts = [
            'preferences' => 'array',
        ];

        public function tickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'requestor_id' );
        }

        public function assignedTickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'assignee_id' );
        }

        public function answers() : HasMany
        {
            return $this->hasMany( Answer::class, 'submitter_id' );
        }


        public function getPreferredThemeAttribute()
        {
            return $this->preferences['theme'] ?? 'light';
        }

        /**
         * @throws \JsonException
         */
        public function setPreferredThemeAttribute( $value ) : void
        {
            $preferences = $this->preferences ?? [];
            $preferences['theme'] = $value;
            $this->attributes['preferences'] = json_encode( $preferences, JSON_THROW_ON_ERROR );
        }

        public function scopeIsAgent( $query )
        {
            return $query->where( 'role', 'agent' );
        }

        public function scopeIsEmployee( $query )
        {
            return $query->where( 'role', 'employee' );
        }

        public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'avatar' )
                ->singleFile()
                ->onlyKeepLatest( 1 )
                ->useFallbackUrl( '/images/default-avatar.jpg' )
                ->useFallbackPath( public_path( '/images/default-avatar.jpg' ) );
        }


    }