<?php
    declare( strict_types = 1 );

    namespace App\Models;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property array<string, mixed> $preferences
     * @property bool $is_locked
     * @property int $lockout_count
     * @property string $preferred_theme
     * @property-read Collection<int, Ticket> $tickets
     */
    class User extends Authenticatable implements HasMedia
    {
        use InteractsWithMedia;
        use SoftDeletes;
        use HasFactory;

        protected $fillable = [
            'name', 'email', 'password', 'role', 'preferences', 'is_locked', 'lockout_time', 'lockout_count'
        ];

        protected $hidden = [ 'password', 'remember_token' ];

        protected $casts = [
            'preferences' => 'array',
            'is_locked' => 'boolean',
            'lockout_time' => 'datetime',
        ];

        /**
         * @return HasMany<Ticket, User>
         */
        public function tickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'requestor_id' );
        }

        /**
         * @return HasMany<Ticket, User>
         */
        public function assignedTickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'assignee_id' );
        }

        /**
         * @return HasMany<Answer, User>
         */
        public function answers() : HasMany
        {
            return $this->hasMany( Answer::class, 'submitter_id' );
        }


        public function getPreferredThemeAttribute() : string
        {
            return $this->preferences['theme'] ?? 'light';
        }

        /**
         * @throws \JsonException
         */
        public function setPreferredThemeAttribute( string $value ) : void
        {
            $preferences = $this->preferences ?? [];
            $preferences['theme'] = $value;
            $this->attributes['preferences'] = json_encode( $preferences, JSON_THROW_ON_ERROR );
        }

        /**
         * @param  Builder<User>  $query
         * @return Builder<User>
         */
        public function scopeIsAgent( $query ) : Builder
        {
            return $query->where( 'role', 'agent' );
        }

        /**
         * @param  Builder<User>  $query
         * @return Builder<User>
         */

        public function scopeIsEmployee( $query ) : Builder
        {
            return $query->where( 'role', 'employee' );
        }

        public function registerMediaCollections() : void
        {
            // Disable fallback in CI
            if ( config( 'app.ci' ) ) {
                $this->addMediaCollection( 'avatar' )
                    ->singleFile()
                    ->onlyKeepLatest( 1 );
                return;
            }

            $this->addMediaCollection( 'avatar' )
                ->singleFile()
                ->onlyKeepLatest( 1 )
                ->useFallbackUrl( url( 'storage/avatar/default-avatar.jpg' ) )
                ->useFallbackPath( storage_path( 'app/public/avatar/default-avatar.jpg' ) );
        }


    }