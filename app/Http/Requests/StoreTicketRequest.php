<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreTicketRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return $this->user()->can( 'create', Ticket::class );
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'attachments.*' => 'file|mimes:jpeg,png|max:2048',
            ];
        }
    }