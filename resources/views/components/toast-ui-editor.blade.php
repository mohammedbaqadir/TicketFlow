@props(['mode' => 'editor', 'content' => '', 'id' => null, 'inputName' => null])

@pushonce('styles')
    @vite('resources/css/toast-ui-editor.css')
@endpushonce

<div id="{{ $id ?? $mode }}"></div>

@if($inputName)
    {{-- Hidden input field to capture editor content --}}
    <input type="hidden" name="{{ $inputName }}" id="{{ $id }}-hidden">
@endif

@pushonce('scripts')
    @vite('resources/js/toast-ui-editor.js');
@endpushonce

@push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const mode = '{{ $mode }}';
        const id = '{{ $id ?? $mode }}';
        const content = @json($content);
        const theme = window.theme || 'light';
        const lang = document.documentElement.getAttribute('lang') || 'en';

        // Initialize the editor or viewer
        window.initializeToastUIEditor(mode, id, content, theme, lang);

        // Handling form submissions for editor mode if there's an associated hidden input
        if (mode === 'editor' && '{{ $inputName }}') {
          const formElement = document.querySelector(`form:has(#${id})`);
          if (formElement) {
            formElement.addEventListener('submit', function (e) {
              const editorInstance = window.toastUIEditors[id];
              if (editorInstance) {
                document.getElementById(`${id}-hidden`).value = editorInstance.getMarkdown();
              }
            });
          }
        }
      });
    </script>
@endpush