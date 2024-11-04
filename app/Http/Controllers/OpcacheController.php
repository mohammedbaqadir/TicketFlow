<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;

    class OpcacheController extends Controller
    {
        public function reset( Request $request ) : JsonResponse
        {
            $response = response()->json( [ 'message' => 'Unauthorized' ], 403 );

            // Check if the user has permission
            if ( $request->user()->tokenCan( 'opcache:reset' ) ) {
                // Attempt to reset OPcache
                if ( opcache_reset() ) {
                    Log::info( 'OPcache reset successful', [
                        'triggered_by' => 'deployment'
                    ] );
                    $response = response()->json( [ 'message' => 'OPcache cleared successfully' ] );
                } else {
                    $response = response()->json( [ 'message' => 'Failed to clear OPcache' ], 500 );
                }
            }

            return $response;
        }

    }