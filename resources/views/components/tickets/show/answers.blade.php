@props(['ticket'])
<div class='grid grid-cols-1 gap-6 p-6 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-gray-700 dark:to-gray-500 rounded-lg shadow-lg mt-8'>
    @can('answer', $ticket)
        <div class="flex justify-end">
            <a href="{{route('tickets.answers.create', $ticket)}}"
               class="relative inline-flex items-center justify-start py-3 pl-4 pr-12 overflow-hidden font-semibold
                  text-purple-600 dark:text-white transition-all duration-150 ease-in-out rounded hover:pl-10 hover:pr-6
                  bg-gray-50 dark:bg-slate-700 group">
            <span class="absolute bottom-0 left-0 w-full h-1 transition-all duration-150 ease-in-out
                   bg-gradient-to-r from-blue-300 to-purple-300 dark:from-blue-300 dark:to-purple-300
                   group-hover:h-full"></span>
                <span class="absolute right-0 pr-4 duration-200 ease-out group-hover:translate-x-12">
                <x-heroicon-o-arrow-right class="w-5 h-5 text-green-300" />
            </span>
                <span class="absolute left-0 pl-2.5 -translate-x-12 group-hover:translate-x-0 ease-out duration-200">
                <x-heroicon-o-arrow-right class="w-5 h-5 text-green-200 dark:text-lime-200" />
            </span>
                <span class="relative w-full text-left transition-colors duration-200 ease-in-out
                   group-hover:text-white">Answer</span>
            </a>
        </div>
    @endcan
    @foreach($ticket->answers as $answer)
        <x-tickets.show.answer :answer="$answer" />
    @endforeach
</div>