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
            $this->startNpm();
            $this->call( 'serve' );
        }

        private function startNpm() : void
        {
            $this->info( 'Starting npm development server...' );
            $npmProcess = new Process( [ 'npm', 'run', 'dev' ] );
            $npmProcess->start();
        }


    }