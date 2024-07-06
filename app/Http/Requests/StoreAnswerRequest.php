<?php
    declare( strict_types = 1 );

    namespace App\Http\Requests;

    use App\Models\Answer;
    use Illuminate\Foundation\Http\FormRequest;

    class StoreAnswerRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize() : bool
        {
            return $this->user()->can( 'create', Answer::class );
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules() : array
        {
            return [
                'content' => [ 'required', 'string' ],
            ];
        }
    }