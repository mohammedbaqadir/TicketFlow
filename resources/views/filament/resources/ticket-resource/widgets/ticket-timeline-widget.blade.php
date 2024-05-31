<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full text-center">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Timeline</h3>
            <ul class="text-gray-900 dark:text-gray-300 space-y-4">
                @foreach($events as $index => $event)
                    @if($index > 0)
                        <div class="border-l-2 border-gray-300 mx-auto" style="width: 2px;"></div>
                    @endif
                    <li class="mb-2">
                        @php
                            $timestamp = $event->created_at;
                            $formattedTime = $timestamp->isToday() ? $timestamp->format('h:i A') : $timestamp->format('M d, h:i A');
                        @endphp
                        <div class="inline-block bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 px-2 py-1 rounded-full mb-2">
                            {{ $formattedTime }}
                        </div>
                        <div>
                            {{ $event->description }}
                        </div>
                        @if (str_contains($event->description, 'Solution submitted by'))
                            @php
                                $solution = $record->solutions()->where('created_at', $event->created_at)->first();
                            @endphp
                            @if ($solution)
                                <div class="p-4 mb-2 bg-gray-100 dark:bg-gray-700 ml-4 rounded text-center relative">
                                    <div class="text-gray-700 dark:text-gray-200 text-sm mb-2">{{ $solution->user->name }}</div>
                                    <p class="mt-2 text-gray-900 dark:text-gray-100">{{ $solution->content }}</p>
                                    <div class="mt-4 flex space-x-2 justify-center">
                                        @foreach($solution->getMedia('solution_attachments') as $media)
                                            @if(strpos($media->mime_type, 'image') === 0)
                                                <a href="#"
                                                   wire:click.prevent="openAttachment('{{ $media->getUrl() }}')">
                                                    <img src="{{ $media->getUrl('thumb') }}"
                                                         alt="Attachment"
                                                         class="w-16 h-16 rounded">
                                                </a>
                                            @else
                                                <a href="{{ $media->getUrl() }}"
                                                   target="_blank"
                                                   class="text-blue-500 underline">
                                                    {{ $media->file_name }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="absolute bottom-2 right-2 flex space-x-2">
                                        @if ($solution->resolved === null)
                                            <button wire:click="markSolutionAsValid({{ $solution->id }})"
                                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full">
                                                ✔️
                                            </button>
                                            <button wire:click="markSolutionAsInvalid({{ $solution->id }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-full">
                                                ❌
                                            </button>
                                        @elseif($solution->resolved === true)
                                            <span class="bg-green-500 text-white text-2xl p-2 rounded-full">✔️</span>
                                        @elseif($solution->resolved === false)
                                            <button wire:click="undoMarking({{ $solution->id }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-full">
                                                ❌
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </x-filament::section>

    <x-filament::modal id="attachmentModal"
                       :visible="$this->attachmentModalVisible"
                       width="lg"
                       wire:model.defer="attachmentModalVisible">
        <x-slot name="header">
            <span class="text-gray-900 dark:text-gray-100">Attachment</span>
        </x-slot>
        <x-slot name="content">
            <img src="{{ $this->currentAttachment }}" alt="Attachment" class="w-full">
        </x-slot>
        <x-slot name="footer">
            <x-filament::button color="gray" wire:click="$toggle('attachmentModalVisible')">Close</x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-widgets::widget>