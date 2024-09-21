<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;

    use App\Helpers\AuthHelper;
    use App\Http\Requests\SearchRequest;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Validation\ValidationException;

    class SearchController extends Controller
    {
        /**
         * Search for tickets based on the provided query.
         *
         * @param  SearchRequest  $request
         * @return JsonResponse
         */
        public function search( SearchRequest $request ) : JsonResponse
        {
            $validatedData = $request->validated();

            $query = $validatedData['query'];
            $page = (int) ( $validatedData['page'] ?? 1 );
            $perPage = (int) ( $validatedData['per_page'] ?? 5 );

            $user = $request->user();
            $results = $this->searchContent( $query, $user, $page, $perPage );

            return response()->json( $results );
        }


        /**
         * Perform the actual search based on user role and query.
         *
         * @param  string  $query  The search query
         * @param  User  $user  The authenticated user
         * @param  int  $page  The current page number
         * @param  int  $perPage  The number of results per page
         * @return array
         */
        private function searchContent( string $query, User $user, int $page, int $perPage ) : array
        {
            $searchQuery = Ticket::search( $query );

            if ( AuthHelper::userHasRole( 'employee' ) ) {
                $searchQuery->where( 'requestor_id', $user->id );
            }

            // Paginate search results
            $paginatedResults = $searchQuery->paginate( $perPage, 'page', $page );

            // Load relationships directly on the paginated result set
            $tickets = Ticket::whereIn( 'id', $paginatedResults->pluck( 'id' ) )
                ->withRelations()
                ->get();

            $formattedResults = $tickets->map( function ( $ticket ) use ( $query ) {
                return [
                    'id' => $ticket->id,
                    'type' => 'ticket',
                    'title' => $ticket->title,
                    'excerpt' => $ticket->generateExcerpt( $query ),
                    'created_at' => $ticket->created_at,
                    'created_by' => $ticket->requestor->name,
                    'assignee' => $ticket->assignee ? $ticket->assignee->name : 'Unassigned',
                ];
            } );

            return [
                'data' => $formattedResults,
                'current_page' => $paginatedResults->currentPage(),
                'last_page' => $paginatedResults->lastPage(),
                'per_page' => $paginatedResults->perPage(),
                'total' => $paginatedResults->total(),
            ];
        }
    }