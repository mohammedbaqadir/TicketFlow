<?php
    declare( strict_types = 1 );

    namespace App\Console\Commands;

    use Exception;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Process;
    use App\Models\Ticket;

    class AppSetup extends Command
    {
        protected $signature = 'app:setup 
        {--retry-count=30 : Number of retries for service checks}
        {--retry-delay=1000 : Delay between retries in milliseconds}';

        protected $description = 'Set up the application, including services, dependencies, database, and caches';

        private $meilisearchProcess;
        private $mysqlProcess;
        private $queueWorkerProcess;

        public function handle() : int
        {
            $this->info( 'Starting application setup...' );

            try {
                $this->startServices();
                $this->checkServices();
                $this->setMeilisearchKey();
                $this->installDependencies();
                $this->setupDatabase();
                $this->setupMeilisearch();
                $this->cacheConfiguration();

                $this->info( 'Application setup complete!' );
                return 0;
            } catch (Exception $e) {
                $this->handleError( $e );
                return 1;
            }
        }

        private function startServices() : void
        {
            $this->info( 'Starting required services...' );

            $this->mysqlProcess = Process::start( 'mysqld' );
            $this->meilisearchProcess = Process::start( 'meilisearch' );
            $this->queueWorkerProcess = Process::start( 'php artisan queue:work' );

            $this->info( 'Services started.' );
        }

        private function checkServices() : void
        {
            $this->info( 'Checking if services are ready...' );

            $retryCount = (int) $this->option( 'retry-count' );
            $retryDelay = (int) $this->option( 'retry-delay' );

            $this->checkDatabase( $retryCount, $retryDelay );
            $this->checkMeilisearch( $retryCount, $retryDelay );
            $this->checkQueueWorker( $retryCount, $retryDelay );

            $this->info( 'All services are ready.' );
        }

        /**
         * @throws \Throwable
         */
        private function checkDatabase( int $retryCount, int $retryDelay ) : void
        {
            retry( $retryCount, function () {
                DB::connection()->getPdo();
            }, $retryDelay );
        }

        /**
         * @throws \Throwable
         */
        private function checkMeilisearch( int $retryCount, int $retryDelay ) : void
        {
            retry( $retryCount, function () {
                $response = Http::get( config( 'scout.meilisearch.host' ) . '/health' );
                if ( $response->status() !== 200 ) {
                    throw new \RuntimeException( 'Meilisearch is not ready' );
                }
            }, $retryDelay );
        }

        /**
         * @throws \Throwable
         */
        private function checkQueueWorker( int $retryCount, int $retryDelay ) : void
        {
            retry( $retryCount, function () {
                if ( !$this->queueWorkerProcess->isRunning() ) {
                    throw new \RuntimeException( 'Queue worker is not running' );
                }
            }, $retryDelay );
        }

        private function setMeilisearchKey() : void
        {
            $meilisearchKey = env( 'MEILI_MASTER_KEY', 'dev_key_placeholder' );
            Config::set( 'scout.meilisearch.key', $meilisearchKey );
            $this->info( 'MEILISEARCH_KEY has been set.' );
        }

        private function installDependencies() : void
        {
            $this->info( 'Installing dependencies...' );
            Process::run( 'composer install' )->throw();
            Process::run( 'npm install' )->throw();
        }

        private function setupDatabase() : void
        {
            $this->info( 'Setting up database...' );
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
            $this->info( 'Caching configuration...' );
            $this->call( 'config:cache' );
            $this->call( 'route:cache' );
            $this->call( 'view:cache' );
        }

        private function handleError( Exception $e ) : void
        {
            $this->error( 'An error occurred during setup: ' . $e->getMessage() );
            $this->cleanup();
        }

        private function cleanup() : void
        {
            $this->info( 'Cleaning up...' );

            $processes = [ $this->mysqlProcess, $this->meilisearchProcess, $this->queueWorkerProcess ];

            foreach ( $processes as $process ) {
                if ( $process && $process->isRunning() ) {
                    $process->stop();
                }
            }

            // Additional cleanup tasks can be added here

            $this->info( 'Cleanup complete.' );
        }

        public function __destruct()
        {
            $this->cleanup();
        }
    }