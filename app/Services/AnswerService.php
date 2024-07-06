<?php
    declare( strict_types = 1 );

    namespace App\Services;

    use App\Models\Answer;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Repositories\AnswerRepository;
    use Exception;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class AnswerService
    {
        protected AnswerRepository $repository;

        public function __construct( AnswerRepository $repository )
        {
            $this->repository = $repository;
        }

        public function create( array $data ) : Answer
        {
            return DB::transaction( function () use ( $data ) {
                $answerData = $this->prepareAnswerData( $data );
                $answer = $this->repository->create( $answerData );

                if ( isset( $data['attachment'] ) ) {
                    $this->handleAttachments( $answer, $data['attachment'] );
                }

                $answer->ticket->update( [ 'status' => 'awaiting-acceptance' ] );

                return $answer->fresh( [ 'submitter', 'ticket', 'comments' ] );
            } );
        }

        public function update( int $id, array $data ) : Answer
        {
            return DB::transaction( function () use ( $id, $data ) {
                $answer = $this->repository->getById( $id );
                if ( !$answer ) {
                    throw new \Exception( "Answer with ID {$id} not found" );
                }

                $answerData = $this->prepareAnswerData( $data, false );
                $this->repository->update( $answer, $answerData );

                $this->handleAttachmentUpdates( $answer, $data );

                return $answer->fresh( [ 'submitter', 'ticket', 'comments' ] );
            } );
        }

        public function delete( int $id ) : bool
        {
            return DB::transaction( function () use ( $id ) {
                $answer = $this->repository->getById( $id );
                if ( !$answer ) {
                    throw new \Exception( "Answer with ID {$id} not found" );
                }

                return $this->repository->delete( $answer );
            } );
        }

        public function acceptAnswer( Answer $answer ) : void
        {
            DB::transaction( function () use ( $answer ) {
                $answer->update( [ 'is_accepted' => true ] );
                $answer->ticket->update( [
                    'status' => 'resolved',
                    'accepted_answer_id' => $answer->id,
                ] );
            } );
        }

        public function getAnswersByTicket(
            Ticket $ticket,
            array $filters = [],
            array $sort = [ 'created_at' => 'desc' ],
            int $perPage = 15
        ) : LengthAwarePaginator {
            return $this->repository->getAnswersByTicket( $ticket->id, $filters, $sort, $perPage,
                [ 'submitter', 'comments' ] );
        }

        private function prepareAnswerData( array $data, bool $isNewAnswer = true ) : array
        {
            $answerData = [ 'content' => $data['content'] ];

            if ( $isNewAnswer ) {
                $answerData['submitter_id'] = auth()->id();
                $answerData['ticket_id'] = $data['ticket_id'];
            }

            return $answerData;
        }

        private function handleAttachmentUpdates( Answer $answer, array $data ) : void
        {
            if ( isset( $data['delete_attachment_id'] ) ) {
                $this->deleteAttachments( $answer, $data['delete_attachment_id'] );
            }

            if ( isset( $data['attachment'] ) ) {
                $this->handleAttachments( $answer, $data['attachment'] );
            }
        }

        private function handleAttachments( Answer $answer, $attachment ) : void
        {
            $answer->addMedia( $attachment )->toMediaCollection( 'answer_attachments' );
        }

        private function deleteAttachments( Answer $answer, $attachmentId ) : void
        {
            $answer->media()->where( 'id', $attachmentId )->first()?->delete();
        }
    }