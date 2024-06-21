
<div class="p-4 rounded-lg ">
    <h3 class="text-lg font-semibold">{{ $solution->user->name }}</h3>
    <p>{{ $solution->content }}</p>
    <p class="text-sm text-gray-600">Created at: {{ $solution->created_at->format('Y-m-d H:i') }}</p>

</div>