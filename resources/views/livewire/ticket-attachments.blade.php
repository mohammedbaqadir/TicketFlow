<div>
    <h3>Attachments</h3>
    <input type="file" wire:model="uploadedFiles" multiple>
    <button wire:click="upload">Upload</button>

    <ul>
        @foreach($attachments as $attachment)
            <li>
                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">{{ $attachment->id }}</a>
                (Uploaded by {{ $attachment->user->name }} at {{ $attachment->created_at }})
                <button wire:click="deleteAttachment({{ $attachment->id }})">Delete</button>
            </li>
        @endforeach
    </ul>
</div>