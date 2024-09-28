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
            Schema::table( 'tickets', function ( Blueprint $table ) {
                $table->index( 'timeout_at' );
                $table->index( 'status' );
            } );
        }

        /**
         * Reverse the migrations.
         */
        public function down() : void
        {
            Schema::table( 'tickets', function ( Blueprint $table ) {
                $table->dropIndex( [ 'timeout_at' ] );
                $table->dropIndex( [ 'status' ] );
            } );
        }
    };