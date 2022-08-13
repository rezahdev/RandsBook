<x-app-layout>
    <x-slot name="header">
        <form class="flex items-center" method="get" action="{{ route('books.search') }}">   
            <input name="searchq" type="text">
            <button type="submit">Submit</button>
        </form>
    </x-slot>

    <div class="py-12">
        <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
                @isset($search_result)
                @foreach($search_result['docs'] as $book)
                <div class="bg-white p-8 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                {{ $book['title'] }}
                </div>
                @endforeach
                @endisset
        </div>
    </div>
</x-app-layout>
