<?php
    declare( strict_types = 1 );

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create( 'tickets', static function ( Blueprint $table ) {
                $table->id();
                $table->string( 'title' );
                $table->longText( 'description' );
                $table->enum( 'status', [ 'open', 'in-progress', 'awaiting-acceptance', 'escalated', 'resolved' ] );
                $table->enum( 'priority', [ 'low', 'medium', 'high' ] );
                $table->dateTime( 'timeout_at' );
                $table->foreignId( 'requestor_id' )->constrained( 'users' )->onDelete( 'cascade' );
                $table->foreignId( 'assignee_id' )->nullable()->constrained( 'users' )->onDelete( 'set null' );
                $table->string( 'meeting_room')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('tickets');
        }
    };