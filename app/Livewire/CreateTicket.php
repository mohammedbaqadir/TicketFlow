<?php

namespace App\Livewire;

    use Livewire\Component;
    use Livewire\WithFileUploads;
    use App\Models\Ticket;
    use App\Models\Attachment;
    use Illuminate\Support\Facades\Auth;

    class CreateTicket extends Component
    {
        use WithFileUploads;

        public $title;
        public $description;
        public $attachments = [];
        public $showModal = false;

        protected $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:2048',
        ];

        public function createTicket()
        {
            $this->validate();

            $ticket = Ticket::create( [
                'title' => $this->title,
                'description' => $this->description,
                'status' => 'open',
                'created_by' => Auth::id(),
                'priority' => TicketHelper::determinePriority( $this->title, $this->description ),
                'timeout_at' => now()->addHours( TicketHelper::determineTimeout( $this->priority ) ),
                'assigned_to' => null,
            ] );

            foreach ( $this->attachments as $file ) {
                $filePath = $file->store( 'attachments' );
                Attachment::create( [
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'file_path' => $filePath,
                ] );
            }


            session()->flash( 'success', 'Ticket created successfully.' );
            $this->reset();
            $this->showModal = false;
            $this->emit( 'ticketCreated' );
        }

        public function render()
        {
            return view( 'livewire.create-ticket' );
        }
    }