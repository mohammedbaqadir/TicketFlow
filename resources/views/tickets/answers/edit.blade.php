@extends('components.layout.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Edit Answer</h1>
                <form action="{{ route('answers.update', $answer) }}" method="POST" id="edit-answer-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="content"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <x-toast-ui-editor mode="editor"
                                           :content="old('content', $answer->content)"
                                           id="answer-editor"
                                           inputName="content" />

                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 px-6 py-3">
                            Update Answer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection