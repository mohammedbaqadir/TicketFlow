@extends('components.layout.app')

@pushonce('styles')
    @vite('resources/css/toast-ui-editor.css')
@endpushonce

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-r from-gray-100 to-purple-100 dark:from-gray-600 dark:to-gray-400 shadow-xl rounded-lg overflow-hidden">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Create New Ticket</h1>
                <form action="{{ route('tickets.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="create-ticket-form">
                    @csrf
                    <div class="mb-4">
                        <label for="title"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text"
                               name="title"
                               id="title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <div id="editor"></div>
                        <input type="hidden" name="description" id="description">
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 px-6 py-3">
                            Create Ticket
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const editor = new Editor({
          el: document.querySelector('#editor'),
          height: '300px',
          initialEditType: 'markdown',
          previewStyle: 'vertical',
          theme: 'default',
          toolbarItems: [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task', 'indent', 'outdent'],
            ['table', 'link'],
            ['code', 'codeblock']]
        });

        document.getElementById('create-ticket-form').addEventListener('submit', function (e) {
          e.preventDefault();
          document.getElementById('description').value = editor.getMarkdown();
          this.submit();
        });
      });
    </script>
@endpush