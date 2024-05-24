<!-- resources/views/livewire/create-ticket.blade.php -->
<div>
    <button @click="showModal = true"
            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded hover:bg-blue-400 transition duration-150">
        Create Ticket
    </button>

    <div x-data="{ showModal: @entangle('showModal') }"
         x-show="showModal"
         class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
        <div class="bg-gray-800 p-6 rounded shadow-lg w-1/3" @click.away="showModal = false">
            <h2 class="text-xl mb-4 text-gray-100">Create Ticket</h2>
            <form wire:submit.prevent="createTicket" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="title" class="block text-gray-400">Title</label>
                    <input type="text"
                           id="title"
                           wire:model.defer="title"
                           class="mt-1 block w-full bg-gray-900 text-gray-300 border-gray-700 rounded focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title') <span class="text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-400">Description</label>
                    <textarea id="description"
                              wire:model.defer="description"
                              class="mt-1 block w-full bg-gray-900 text-gray-300 border-gray-700 rounded focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('description') <span class="text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="attachments" class="block text-gray-400">Attachments</label>
                    <input type="file"
                           id="attachments"
                           wire:model="attachments"
                           multiple
                           class="mt-1 block w-full bg-gray-900 text-gray-300 border-gray-700 rounded focus:border-indigo-500 focus:ring-indigo-500">
                    @error('attachments.*') <span class="text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button"
                            @click="showModal = false"
                            class="bg-gray-600 text-white px-4 py-2 rounded mr-2 hover:bg-gray-500 transition duration-150">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded hover:bg-blue-400 transition duration-150">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>