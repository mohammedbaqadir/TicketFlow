<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;

    class UserPreferenceController extends Controller
    {
        public function index() : View
        {
            return view( 'preferences' );
        }

        public function updateTheme( Request $request ) : JsonResponse
        {
            $request->validate( [
                'theme' => 'required|in:light,dark',
            ] );

            /** @var User|null $user */
            $user = auth()->user();

            if ( !$user ) {
                return response()->json( [ 'error' => 'Unauthenticated' ], 401 );
            }

            $user->preferred_theme = $request->theme;
            $user->save();

            return response()->json( [ 'success' => true, 'theme' => $user->preferred_theme ] );
        }


    }