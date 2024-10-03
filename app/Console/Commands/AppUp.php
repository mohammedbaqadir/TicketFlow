<?php
    declare( strict_types = 1 );

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Symfony\Component\Process\Process;

    class AppUp extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'app:up';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Start MySQL, Meilisearch, npm, and serve the application';

        /**
         * Execute the console command.
         */
        public function handle() : void
        {
            $this->info( 'Starting application services...' );
            $this->startService( 'MySQL', 'service mysql start', 'MySQL started', 'Failed to start MySQL' );
            $this->startMeilisearch();
            $this->startNpm();
            $this->call( 'serve' );
        }

        private function startService(
            string $serviceName,
            string $command,
            string $successMessage,
            string $failureMessage
        ) : void {
            $process = new Process( explode( ' ', $command ) );
            $process->run();
            if ( $process->isSuccessful() ) {
                $this->info( $successMessage );
            } else {
                $this->error( $failureMessage );
            }
        }

        private function startMeilisearch() : void
        {
            // Meilisearch key is already set by AppSetup and stored in config
            $meilisearchKey = config( 'scout.meilisearch.key',
                'password' );  // Fallback to 'password' if key is not found
            $meilisearchProcess = new Process( [ 'meilisearch', '--master-key=' . $meilisearchKey ] );
            $meilisearchProcess->start();
            $this->info( 'Meilisearch started with the configured master key.' );
        }

        private function startNpm() : void
        {
            $this->info( 'Starting npm development server...' );
            $npmProcess = new Process( [ 'npm', 'run', 'dev' ] );
            $npmProcess->start();
        }


    }