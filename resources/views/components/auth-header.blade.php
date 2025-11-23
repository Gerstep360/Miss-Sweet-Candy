@props(['title', 'description'])

<div class="text-center mb-8">
    <h1 class="text-2xl font-bold text-white mb-2">{{ $title }}</h1>
    @if($description)
        <p class="text-zinc-400">{{ $description }}</p>
    @endif
</div>
