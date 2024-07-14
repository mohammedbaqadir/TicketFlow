@props(['ticket'])

<x-modal>
    <x-slot name="trigger">
        <button type="button"
                class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition duration-200 transform hover:scale-110 hover:shadow-lg">
            <x-heroicon-o-trash class="w-5 h-5 sm:w-6 sm:h-6" />
        </button>
    </x-slot>

    <x-slot name="title">Confirm Deletion</x-slot>

    <x-slot name="content"><p>Are you sure you want to delete this ticket?</p></x-slot>

    <x-slot name="footer">
        <form method="POST"
              action="{{ route('tickets.destroy', $ticket) }}"
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