<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Helpers\AuthHelper;
    use Illuminate\Support\Facades\Auth;

    class ProfileController extends Controller
    {
        public function index()
        {
            $user = Auth::user();

            if ( $user === null ) {
                abort( 403, 'Unauthorized access' );
            }

            $role = $user->role;

            $stats = [
                'tickets' => [],
                'answers' => []
            ];

            if ( AuthHelper::userHasRole( 'employee' ) ) {
                $ticketCounts = optional( $user->tickets() )
                    ->selectRaw( 'status, COUNT(*) as count' )
                    ->groupBy( 'status' )
                    ->pluck( 'count', 'status' );

                $stats['tickets'] = [
                    'total' => $ticketCounts->sum(),
                    'open' => $ticketCounts->get( 'open', 0 ),
                    'in_progress' => $ticketCounts->get( 'in-progress', 0 ),
                    'awaiting_acceptance' => $ticketCounts->get( 'awaiting-acceptance', 0 ),
                    'escalated' => $ticketCounts->get( 'escalated', 0 ),
                    'resolved' => $ticketCounts->get( 'resolved', 0 ),
                ];
            }

            if ( AuthHelper::userHasRole( 'agent' ) ) {
                $ticketCounts = optional( $user->assignedTickets() )
                    ->selectRaw( 'status, COUNT(*) as count' )
                    ->groupBy( 'status' )
                    ->pluck( 'count', 'status' );

                $stats['tickets'] = [
                    'total' => $ticketCounts->sum(),
                    'open' => $ticketCounts->get( 'open', 0 ),
                    'in_progress' => $ticketCounts->get( 'in-progress', 0 ),
                    'resolved' => $ticketCounts->get( 'resolved', 0 ),
                ];

                $totalAnswers = optional( $user->answers() )->count();
                $acceptedAnswers = optional( $user->answers() )
                    ->whereHas( 'ticket', function ( $query ) {
                        $query->whereColumn( 'accepted_answer_id', 'answers.id' );
                    } )->count();

                $stats['answers'] = [
                    'total' => $totalAnswers,
                    'accepted' => $acceptedAnswers,
                ];
            }

            return view( 'profile.index', compact( 'user', 'stats', 'role' ) );
        }


    }