@props(['answer'])

<div class="w-full rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out hover:shadow-xl
            bg-gradient-to-r from-purple-200 to-purple-300 dark:from-slate-800 dark:to-slate-700
            @if($answer->is_accepted) border-2 border-green-400 dark:border-green-600 glow-green-400
            dark:glow-green-600 @endif">
    {{-- Header Section --}}
    <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <img src="{{ $answer->submitter->getFirstMediaUrl('avatar') }}"
                 alt="{{ $answer->submitter->name }}"
                 class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-600 shadow-md">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $answer->submitter->name }}</h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center text-sm text-gray-600 dark:text-gray-300 space-y-1 sm:space-y-0 sm:space-x-2">
                    <span>{{ $answer->created_at->diffForHumans() }}</span>
                    @if($answer->updated_at->ne($answer->created_at))
                        <span class="hidden sm:inline">&bull;</span>
                        <span>Edited {{ $answer->updated_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
        </div>
        @if($answer->is_accepted)
            <div class="flex items-center space-x-1 bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm font-medium shadow-sm">
                <x-heroicon-o-check-badge class="w-4 h-4 sm:w-5 sm:h-5 mr-1" />
                <span>Accepted</span>
            </div>
        @endif
    </div>

    {{-- Content Section --}}
    <div class="p-6">
        {!!Str::markdown($answer->content, [
            'html_input' => 'strip',
])!!}
    </div>

    {{-- Actions Section --}}
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end items-center space-x-4">
        @can('accept', $answer)
            @if(!$answer->is_accepted)
                <x-modal>
                    <x-slot name="trigger">
                        <button type="button"
                                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition duration-200 transform hover:scale-110 hover:shadow-lg">
                            <x-heroicon-o-check class="w-5 h-5 sm:w-6 sm:h-6 mr-1" />
                        </button>
                    </x-slot>

                    <x-slot name="title">Confirm Acceptance</x-slot>

                    <x-slot name="content">
                        <p>This will resolve the ticket <strong>permanently</strong> , Are you sure ?</p>
                    </x-slot>

                    <x-slot name="footer">
                        <form method="POST"
                              action="{{ route('answers.accept', $answer) }}"
                              class="sm:flex sm:flex-row-reverse">
                            @csrf
                            <button type="submit"
                                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium
                                    text-white bg-green-600 border border-transparent rounded-md shadow-sm
                                    hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                                    focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Accept
                            </button>
                        </form>
                    </x-slot>
                </x-modal>
            @endif
        @endcan

        @can('update', $answer)
            <a href="{{route('answers.edit', $answer)}}">
                <button type="button"
                        class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200
                        transition duration-200 transform hover:scale-110 hover:shadow-lg mb-1.5">
                    <x-heroicon-o-pencil-square class="w-5 h-5 sm:w-6 sm:h-6" />
                </button>
            </a>
        @endcan

        @can('delete', $answer)
            <x-modal>
                <x-slot name="trigger">
                    <button type="button"
                            class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition duration-200 transform hover:scale-110 hover:shadow-lg">
                        <x-heroicon-o-trash class="w-5 h-5 sm:w-6 sm:h-6" />
                    </button>
                </x-slot>

                <x-slot name="title">Confirm Deletion</x-slot>

                <x-slot name="content"><p>Are you sure you want to delete this answer?</p></x-slot>

                <x-slot name="footer">
                    <form method="POST"
                          action="{{ route('answers.destroy', $answer) }}"
                          class="sm:flex sm:flex-row-reverse">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                </x-slot>
            </x-modal>
        @endcan
    </div>
</div>