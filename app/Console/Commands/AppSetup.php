<?php
    declare( strict_types = 1 );

    namespace App\Console\Commands;

    use Exception;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Config;
    use App\Models\Ticket;

    class AppSetup extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'app:setup';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Set up the application, including database, Meilisearch, and caches';

        /**
         * Execute the console command.
         */
        public function handle() : void
        {
            $this->info( 'Starting application setup...' );

            // Generate Meilisearch key and set it programmatically
            if ( !$this->setMeilisearchKey() ) {
                $this->error( 'Failed to set MEILISEARCH_KEY.' );
                return;
            }

            $exitCode = $this->runSetupTasks();
            $this->info( $exitCode === 0 ? 'Application setup complete!' : 'Application setup failed.' );
        }

        private function setMeilisearchKey() : bool
        {
            $meilisearchKey = env( 'MEILI_MASTER_KEY', 'dev_key_placeholder' );
            Config::set( 'scout.meilisearch.key', $meilisearchKey );
            $this->info( 'MEILISEARCH_KEY has been set programmatically.' );
            return true;
        }

        private function runSetupTasks() : int
        {
            try {
                // Migrate and seed the database
                $this->runDatabaseMigrations();

                // Set up Meilisearch
                $this->setupMeilisearch();

                // Cache configurations
                $this->cacheConfiguration();
            } catch (Exception $e) {
                $this->error( 'An error occurred during setup: ' . $e->getMessage() );
                return 1;
            }

            return 0;
        }

        private function runDatabaseMigrations() : void
        {
            $this->info( 'Running database migrations and seeding...' );
            $this->call( 'migrate:fresh', [ '--seed' => true ] );
        }

        private function setupMeilisearch() : void
        {
            $this->info( 'Setting up Meilisearch...' );
            $this->call( 'scout:flush', [ 'model' => Ticket::class ] );
            $this->call( 'scout:import', [ 'model' => Ticket::class ] );
        }

        private function cacheConfiguration() : void
        {
            $this->info( 'Caching configuration, routes, and views...' );
            $this->call( 'config:cache' );
            $this->call( 'route:cache' );
            $this->call( 'view:cache' );
        }


    }