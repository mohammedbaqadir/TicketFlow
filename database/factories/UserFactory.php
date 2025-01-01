<?php

    declare( strict_types = 1 );

    namespace Database\Factories;

    use App\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Log;
    use Xchimx\UnsplashApi\Facades\Unsplash;

    class UserFactory extends Factory
    {
        protected $model = User::class;

        private ?string $gender = null;

        /**
         * Generate a random gender for the user.
         */
        private function getGender() : string
        {
            if ( !$this->gender ) {
                $this->gender = $this->faker->randomElement( [ 'male', 'male', 'female' ] );
            }

            return $this->gender;
        }

        /**
         * Core user definition.
         */
        public function definition() : array
        {
            $firstName = $this->faker->firstName( $this->getGender() );

            return [
                'name' => "{$firstName} {$this->faker->lastName()}",
                'email' => strtolower( $firstName ) . '@skyfleet.com',
                'password' => bcrypt( 'password' ),
                'role' => Arr::random( [ 'employee', 'agent' ] ),
            ];
        }

        /**
         * Configure factory hooks.
         */
        public function configure() : self
        {
            return $this->afterCreating( function ( User $user ) {
                $this->assignAvatar( $user );
            } );
        }

        /**
         * Assign an avatar to the user.
         */
        private function assignAvatar( User $user ) : void
        {
            // Skip avatar assignment in CI
            if ( env( 'CI', false ) ) {
                return;
            }

            try {
                $gender = $this->getGender();
                $firstName = explode( ' ', $user->name )[0];

                // Attempt Unsplash search
                $searchQuery = $gender === 'male'
                    ? "professional {$firstName} headshot man"
                    : "professional {$firstName} headshot woman";

                $response = Unsplash::searchPhotosAdvanced( [
                    'query' => $searchQuery,
                    'orientation' => 'squarish',
                    'per_page' => 1,
                ] );

                // Fallback to generic search if no results
                if ( empty( $response['results'] ) ) {
                    $searchQuery = $gender === 'male'
                        ? 'professional headshot man'
                        : 'professional headshot woman';

                    $response = Unsplash::searchPhotosAdvanced( [
                        'query' => $searchQuery,
                        'orientation' => 'squarish',
                        'per_page' => 1,
                    ] );

                    if ( empty( $response['results'] ) ) {
                        throw new \RuntimeException( 'No photos found matching the criteria' );
                    }
                }

                $photo = $response['results'][0];
                $imageUrl = $photo['urls']['small'] ?? Unsplash::getPhotoDownloadLink( $photo['id'] );

                $user->addMediaFromUrl( $imageUrl )
                    ->toMediaCollection( 'avatar' );
            } catch (\Exception $e) {
                Log::warning( 'Failed to fetch Unsplash photo for user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ] );

                // Fallback to default avatar
                $user->addMediaFromUrl( $user->getFirstMediaUrl( 'avatar', 'fallback' ) )
                    ->toMediaCollection( 'avatar' );
            }
        }
    }