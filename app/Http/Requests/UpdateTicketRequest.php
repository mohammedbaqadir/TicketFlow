<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class UpdateTicketRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return $this->user()->can( 'update', $this->route('ticket')) ;
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules() : array
        {
            return [
                'title' => [ 'sometimes', 'string', 'max:255' ],
                'description' => [ 'sometimes', 'string' ],
            ];
        }

    }