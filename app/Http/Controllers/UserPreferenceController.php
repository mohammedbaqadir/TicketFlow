<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class UserPreferenceController extends Controller
    {
        public function index()
        {
            return view( 'preferences' );
        }

        public function updateTheme( Request $request ) : JsonResponse
        {
            $request->validate( [
                'theme' => 'required|in:light,dark',
            ] );

            $user = Auth::user();
            $user->preferred_theme = $request->theme;
            $user->save();

            return response()->json( [ 'success' => true, 'theme' => $user->preferred_theme ] );
        }


    }