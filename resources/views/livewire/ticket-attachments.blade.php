<!-- resources/views/livewire/ticket-attachments.blade.php -->
<div class="p-4 bg-gray-800 rounded-lg shadow-lg">
    <h3 class="text-lg text-gray-100 mb-4">Attachments</h3>
    <input type="file"
           wire:model="uploadedFiles"
           multiple
           class="mb-4 bg-gray-900 text-gray-300 border-gray-700 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
    <button wire:click="upload"
            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded hover:bg-blue-400 transition duration-150">
        Upload
    </button>

    <ul class="mt-4 text-gray-300">
        @foreach($attachments as $attachment)
            <li class="mb-2 flex justify-between items-center">
                <a href="{{ Storage::url($attachment->file_path) }}"
                   target="_blank"
                   class="hover:underline">{{ $attachment->id }}</a>
                <span>(Uploaded by {{ $attachment->user->name }} at {{ $attachment->created_at }})</span>
                <button wire:click="deleteAttachment({{ $attachment->id }})"
                        class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-500 transition duration-150">Delete
                </button>
            </li>
        @endforeach
    </ul>
</div>