<?php

namespace App\Livewire;


    use Livewire\Component;
    use Livewire\WithPagination;
    use App\Models\Ticket;
    use Illuminate\Support\Facades\Auth;

    class MyTickets extends Component
    {
        use WithPagination;

        public $search = '';
        public $statusFilter = '';
        public $priorityFilter = '';

        protected $queryString = [
            'search' => [ 'except' => '' ],
            'statusFilter' => [ 'except' => '' ],
            'priorityFilter' => [ 'except' => '' ],
        ];

        public function updatingSearch()
        {
            $this->resetPage();
        }

        public function updatingStatusFilter()
        {
            $this->resetPage();
        }

        public function updatingPriorityFilter()
        {
            $this->resetPage();
        }

        public function render()
        {
            $tickets = Ticket::query()
                ->where( 'created_by', Auth::id() )
                ->when( $this->search, function ( $query ) {
                    $query->where( function ( $query ) {
                        $query->where( 'title', 'like', '%' . $this->search . '%' )
                            ->orWhere( 'description', 'like', '%' . $this->search . '%' );
                    } );
                } )
                ->when( $this->statusFilter, function ( $query ) {
                    $query->where( 'status', $this->statusFilter );
                } )
                ->when( $this->priorityFilter, function ( $query ) {
                    $query->where( 'priority', $this->priorityFilter );
                } )
                ->paginate( 10 );

            return view( 'livewire.my-tickets', [
                'tickets' => $tickets,
            ] )->layout( 'layouts.app' );
        }
    }