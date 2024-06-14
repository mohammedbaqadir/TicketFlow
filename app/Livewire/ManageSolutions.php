<?php

    namespace App\Livewire;

    use App\Models\Attachment;
    use App\Models\Solution;
    use App\Models\Ticket;
    use Livewire\Component;
    use Livewire\WithFileUploads;

    class ManageSolutions extends Component
    {
        use WithFileUploads;

        public $ticket;
        public $content;
        public $attachments = [];

        public function mount( Ticket $ticket )
        {
            $this->ticket = $ticket;
        }

        public function submitSolution()
        {
            $solution = Solution::create( [
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'content' => $this->content,
            ] );

            foreach ( $this->attachments as $file ) {
                $path = $file->store( 'attachments' );

                Attachment::create( [
                    'solution_id' => $solution->id,
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                ] );
            }

            $this->ticket->update( [ 'status' => 'awaiting-acceptance' ] );

            session()->flash( 'message', 'Solution submitted successfully.' );
        }

        public function markSolutionAsValid( Solution $solution )
        {
            $solution->update( [ 'resolved' => true ] );
            $solution->ticket->update( [ 'status' => 'closed' ] );

            session()->flash( 'message', 'Solution marked as valid and ticket closed.' );
        }

        public function markSolutionAsInvalid( Solution $solution ) : void
        {
            $solution->update( [ 'resolved' => false ] );
            $solution->ticket->update( [ 'status' => 'in-progress' ] );
        }

        public function undoMarking( Solution $solution )
        {
            $solution->update( [ 'resolved' => null ] );
        }

        public function render()
        {
            return view( 'livewire.manage-solutions', [
                'solutions' => $this->ticket->solutions,
            ] );
        }
    }