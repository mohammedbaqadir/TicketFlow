<?php

    namespace App\Http\Controllers;

    use App\Helpers\AuthHelper;
    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;

    class SearchController extends Controller
    {
        public function search( Request $request )
        {
            $query = $request->input( 'query' );

            if ( empty( $query ) ) {
                return response()->json( [] );
            }

            $user = Auth::user();
            $results = $this->searchContent( $query, $user );

            return response()->json( $results );
        }

        private function searchContent( $query, $user )
        {
            $result = collect();
            $userRole = $this->getUserRole();

            if ( $userRole === 'employee' ) {
                $result = $this->searchTickets( $query, $user->id, [ 'created_by', 'assignee_id' ] );
            } elseif ( $userRole === 'agent' ) {
                $result = $this->searchTickets( $query, $user->id, [ 'assignee_id', null ] );
            }

            return $result;
        }

        private function getUserRole()
        {
            $role = 'other';

            if ( AuthHelper::userHasRole( 'employee' ) ) {
                $role = 'employee';
            } elseif ( AuthHelper::userHasRole( 'agent' ) ) {
                $role = 'agent';
            }

            return $role;
        }

        private function searchTickets( $query, $userId, $userColumns )
        {
            return Ticket::where( function ( $q ) use ( $userId, $userColumns ) {
                foreach ( $userColumns as $column ) {
                    if ( $column ) {
                        $q->orWhere( $column, $userId );
                    } else {
                        $q->orWhereNull( $column );
                    }
                }
            } )
                ->where( function ( $q ) use ( $query ) {
                    $q->where( 'title', 'like', "%{$query}%" )
                        ->orWhere( 'description', 'like', "%{$query}%" );
                } )
                ->with( 'requestor' )
                ->get()
                ->map( function ( $ticket ) use ( $query ) {
                    $inTitle = stripos( $ticket->title, $query ) !== false;
                    $excerpt = $inTitle
                        ? $this->generateExcerpt( $ticket->title, $query )
                        : $this->generateExcerpt( $ticket->description, $query );

                    return [
                        'id' => $ticket->id,
                        'type' => 'ticket',
                        'title' => $ticket->title,
                        'excerpt' => $excerpt,
                        'created_at' => $ticket->created_at,
                        'created_by' => $ticket->requestor->name,
                        'assignee_id' => $ticket->assignee_id ? User::find( $ticket->assignee_id )->name : 'Unassigned',
                    ];
                } );
        }

        private function generateExcerpt( $content, $query )
        {
            $position = stripos( $content, $query );
            if ( $position === false ) {
                return substr( $content, 0, 100 );
            }

            $start = max( 0, $position - 50 );
            $excerpt = substr( $content, $start, 100 );

            if ( $start > 0 ) {
                $excerpt = '...' . $excerpt;
            }
            if ( \strlen( $content ) > $start + 100 ) {
                $excerpt .= '...';
            }

            return $excerpt;
        }
    }