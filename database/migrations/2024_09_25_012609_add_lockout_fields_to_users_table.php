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
            Schema::table( 'users', function ( Blueprint $table ) {
                $table->boolean( 'is_locked' )->default( false );
                $table->timestamp( 'lockout_time' )->nullable();
                $table->integer( 'lockout_count' )->default( 0 );
            } );
        }

        /**
         * Reverse the migrations.
         */
        public function down() : void
        {
            Schema::table( 'users', function ( Blueprint $table ) {
                $table->dropColumn( 'is_locked' );
                $table->dropColumn( 'lockout_time' );
                $table->dropColumn( 'lockout_count' );
            } );
        }
    };