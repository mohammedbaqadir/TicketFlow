<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class SearchRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize() : bool
        {
            return auth()->check();
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules() : array
        {
            return [
                'query' => 'required|string|max:255',
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100',
            ];
        }

        /**
         * Prepare the data for validation.
         */
        protected function prepareForValidation() : void
        {
            $this->merge( [
                'page' => $this->input( 'page', 1 ),
                'per_page' => $this->input( 'per_page', 15 ),
            ] );
        }

    }