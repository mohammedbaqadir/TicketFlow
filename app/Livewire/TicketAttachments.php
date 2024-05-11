<?php

    namespace App\Livewire;

    use App\Models\Attachment;
    use App\Models\Ticket;
    use Livewire\Component;
    use Livewire\WithFileUploads;

    class TicketAttachments extends Component
    {
        use WithFileUploads;

        public Ticket $ticket;
        public $attachments = [];
        public $uploadedFiles = [];

        public function mount( Ticket $ticket )
        {
            $this->ticket = $ticket;
            $this->attachments = $ticket->attachments;
        }

        public function upload()
        {
            foreach ( $this->uploadedFiles as $file ) {
                $path = $file->store( 'attachments' );

                Attachment::create( [
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->user()->id,
                    'file_path' => $path,
                ] );
            }

            $this->attachments = $this->ticket->attachments;
            $this->uploadedFiles = [];
        }

        public function deleteAttachment( $id ) : void
        {
            $attachment = Attachment::findOrFail( $id );
            $attachment->delete();

            $this->attachments = $this->ticket->attachments;
        }

        public function render()
        {
            return view( 'livewire.ticket-attachments' );
        }
    }