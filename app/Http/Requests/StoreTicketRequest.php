<?php
    declare( strict_types = 1 );

    namespace App\Http\Requests;

    use App\Models\Ticket;
    use Illuminate\Foundation\Http\FormRequest;

    class StoreTicketRequest extends FormRequest
    {
        public function authorize() : bool
        {
            return $this->user()->can( 'create', Ticket::class );
        }

        /**
         * @return array<string, array<int, string>>
         */

        public function rules() : array
        {
            return [
                'title' => [ 'required', 'string', 'max:255' ],
                'description' => [ 'required', 'string' ],
            ];
        }

    }