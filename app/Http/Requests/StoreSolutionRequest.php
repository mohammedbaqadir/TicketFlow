<?php

    namespace App\Http\Requests;

    use App\Models\Solution;
    use Illuminate\Foundation\Http\FormRequest;

    class StoreSolutionRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return $this->user()->can( 'create', [ Solution::class, $this->route( 'ticket' ) ] );
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            return [
                'content' => 'required|string',
                'attachments.*' => 'file|mimes:jpeg,png,pdf,doc,docx|max:2048',
            ];
        }
    }