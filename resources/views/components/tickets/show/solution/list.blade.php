@props(['ticket'])

<div class="mt-8 bg-gradient-to-r from-indigo-100 to-purple-100  dark:from-gray-700 dark:to-gray-500 p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Solutions</h2>
    @forelse($ticket->solutions as $solution)
        <x-tickets.show.solution.card
                :solution="$solution"
                :canRate="$ticket->isRequestor(auth()->user()) && $solution->resolved === null"
        />
    @empty
        <p class="text-gray-500 dark:text-gray-400">No solutions submitted yet.</p>
    @endforelse
</div>