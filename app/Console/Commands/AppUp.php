<?php
    declare( strict_types = 1 );

    namespace App\Console\Commands;

    use Exception;
    use Illuminate\Console\Command;
    use Symfony\Component\Process\Process;

    class AppUp extends Command
    {
        protected $signature = 'app:up
        {--npm-port=3000 : Port for npm dev server}
        {--serve-host=127.0.0.1 : Host for Laravel serve}
        {--serve-port=8000 : Port for Laravel serve}';

        protected $description = 'Start npm and serve the application';

        private $npmProcess;
        private $serveProcess;

        public function handle() : int
        {
            try {
                $this->info( 'Starting application services...' );

                $this->startNpm();
                $this->startServe();

                $this->waitForShutdown();

                return 0;
            } catch (Exception $e) {
                $this->handleError( $e );
                return 1;
            }
        }

        private function startNpm() : void
        {
            $port = $this->option( 'npm-port' );
            $this->info( "Starting npm development server on port {$port}..." );
            $this->npmProcess = new Process( [ 'npm', 'run', 'dev', '--', '--port', $port ] );
            $this->npmProcess->start( function ( $type, $buffer ) {
                $this->output->write( $buffer );
            } );
        }

        private function startServe() : void
        {
            $host = $this->option( 'serve-host' );
            $port = $this->option( 'serve-port' );
            $this->info( "Starting Laravel development server on {$host}:{$port}..." );
            $this->serveProcess = new Process( [ 'php', 'artisan', 'serve', "--host={$host}", "--port={$port}" ] );
            $this->serveProcess->start( function ( $type, $buffer ) {
                $this->output->write( $buffer );
            } );
        }

        private function waitForShutdown() : void
        {
            while ( $this->npmProcess->isRunning() && $this->serveProcess->isRunning() ) {
                usleep( 1000000 ); // Sleep for 1 second
            }

            $this->shutdown();
        }

        private function shutdown() : void
        {
            $this->info( 'Shutting down application services...' );

            $this->stopProcess( $this->npmProcess, 'npm' );
            $this->stopProcess( $this->serveProcess, 'Laravel serve' );

            $this->info( 'Application services have been shut down.' );
        }

        private function stopProcess( ?Process $process, string $name ) : void
        {
            if ( $process && $process->isRunning() ) {
                $this->info( "Stopping {$name} process..." );
                $process->stop();
                $this->info( "{$name} process stopped." );
            }
        }

        private function handleError( Exception $e ) : void
        {
            $this->error( 'An error occurred while running the application:' );
            $this->error( $e->getMessage() );
            $this->error( $e->getTraceAsString() );
            $this->shutdown();
        }

        public function __destruct()
        {
            $this->shutdown();
        }
    }