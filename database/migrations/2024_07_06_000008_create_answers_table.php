<?php
    declare( strict_types = 1 );

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         */
        public function up() : void
        {
            Schema::create( 'answers', function ( Blueprint $table ) {
                $table->id();
                $table->text( 'content' );
                $table->boolean( 'is_accepted' )->default( false );
                $table->foreignId( 'submitter_id' )->constrained( 'users' )->onDelete( 'cascade' );
                $table->foreignId( 'ticket_id' )->constrained();
                $table->timestamps();
                $table->softDeletes();
            } );
        }

        /**
         * Reverse the migrations.
         */
        public function down() : void
        {
            Schema::dropIfExists( 'answers' );
        }
    };