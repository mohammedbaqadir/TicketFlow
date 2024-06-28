<?php
    declare( strict_types = 1 );

    namespace App\Services;

    use App\Models\Solution;
    use App\Models\Ticket;
    use App\Models\User;
    use Illuminate\Support\Facades\Log;

    class SolutionService
    {
        public function submitSolution( Ticket $ticket, User $user, array $data ) : Solution
        {
            DB::beginTransaction();
            try {
                $solution = Solution::create( [
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'content' => $data['content'],
                ] );

                if ( isset( $data['attachments'] ) ) {
                    foreach ( $data['attachments'] as $attachment ) {
                        $solution->addMedia( $attachment )->toMediaCollection( 'solution_attachments' );
                    }
                }

                $ticket->update( [ 'status' => 'awaiting-acceptance' ] );

                DB::commit();
                return $solution;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error( 'Error submitting solution: ' . $e->getMessage() );
                throw $e;
            }
        }

        public function updateSolutionContent( Solution $solution, array $data ) : array
        {
            DB::beginTransaction();
            try {
                $solution->update( [
                    'content' => $data['content'],
                ] );

                if ( isset( $data['attachments'] ) ) {
                    foreach ( $data['attachments'] as $attachment ) {
                        $solution->addMedia( $attachment )->toMediaCollection( 'solution_attachments' );
                    }
                }

                DB::commit();
                return [ 'success' => true, 'message' => 'Solution content updated successfully' ];
            } catch (Exception $e) {
                DB::rollBack();
                Log::error( 'Error updating solution content: ' . $e->getMessage() );
                return [ 'success' => false, 'message' => 'An error occurred while updating the solution content' ];
            }
        }

        public function updateSolutionResolved( Solution $solution, bool $resolved ) : array
        {
            try {
                if ( $resolved ) {
                    $solution->markValid();
                } else {
                    $solution->markInvalid();
                }

                return [ 'success' => true, 'message' => 'Solution status updated successfully' ];
            } catch (Exception $e) {
                Log::error( 'Error updating solution status: ' . $e->getMessage() );
                return [ 'success' => false, 'message' => 'An error occurred while updating the solution status' ];
            }
        }

        public function deleteSolution( Solution $solution ) : bool
        {
            try {
                return $solution->delete();
            } catch (Exception $e) {
                Log::error( 'Error deleting solution: ' . $e->getMessage() );
                return false;
            }
        }


    }