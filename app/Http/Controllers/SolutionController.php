<?php
    declare( strict_types = 1 );

    namespace App\Http\Controllers;


    use App\Services\SolutionService;

    class SolutionController extends Controller
    {
        protected SolutionService $solutionService;

        public function __construct( SolutionService $solutionService )
        {
            $this->solutionService = $solutionService;
        }

        public function create( Ticket $ticket )
        {
            $this->authorize( 'create', [ Solution::class, $ticket ] );
            return view( 'solutions.create', compact( 'ticket' ) );
        }

        public function store( StoreSolutionRequest $request, Ticket $ticket )
        {
            $solution = $this->solutionService->submitSolution( $ticket, Auth::user(), $request->validated() );
            return redirect()->route( 'tickets.show', $ticket )->with( 'success', 'Solution submitted successfully.' );
        }

        public function update( Request $request, Solution $solution )
        {
            $this->authorize( 'update', $solution );

            if ( $solution->user_id === Auth::id() ) {
                // Solution creator updating content and attachments
                $validated = $request->validate( [
                    'content' => 'required|string',
                    'attachments.*' => 'file|mimes:jpeg,png,pdf,doc,docx|max:2048',
                ] );
                $result = $this->solutionService->updateSolutionContent( $solution, $validated );
            } elseif ( $solution->ticket->isRequestor( Auth::user() ) ) {
                // Ticket requestor updating 'resolved' attribute
                $validated = $request->validate( [
                    'resolved' => 'required|boolean',
                ] );
                $result = $this->solutionService->updateSolutionResolved( $solution, $validated['resolved'] );
            } else {
                return response()->json( [ 'error' => 'Unauthorized' ], 403 );
            }

            if ( $result['success'] ) {
                return response()->json( [ 'message' => 'Solution updated successfully' ] );
            }
            return response()->json( [ 'error' => $result['message'] ], 400 );
        }

        public function destroy( Solution $solution )
        {
            $this->authorize( 'delete', $solution );
            $result = $this->solutionService->deleteSolution( $solution );
            if ( $result ) {
                return redirect()->route( 'tickets.show', $solution->ticket )->with( 'success',
                    'Solution deleted successfully.' );
            }
            return back()->withErrors( 'Failed to delete the solution.' );
        }


    }