<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Spatie\EloquentSortable\Sortable;
    use Spatie\EloquentSortable\SortableTrait;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    class User extends Authenticatable implements HasMedia
    {
        use HasFactory;
        use Notifiable;
        use InteractsWithMedia;


        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'role',
        ];

        /**
         * The attributes that should be hidden for serialization.
         *
         * @var array<int, string>
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * Get the attributes that should be cast.
         *
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
            ];
        }


        public function registerMediaCollections() : void
        {
            $this->addMediaCollection( 'avatar' )
                ->singleFile()
                ->useFallbackUrl( '/images/default-avatar.jpg' )
                ->useFallbackPath( public_path( '/images/default-avatar.jpg' ) );
        }

        public function scopeIsAgent( $query )
        {
            return $query->where( 'role', 'agent' );
        }

        public function scopeIsEmployee( $query )
        {
            return $query->where( 'role', 'employee' );
        }


        public function createdTickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'created_by' );
        }

        public function assignedTickets() : HasMany
        {
            return $this->hasMany( Ticket::class, 'assigned_to' );
        }

    }