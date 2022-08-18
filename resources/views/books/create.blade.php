<x-app-layout>
    <x-slot name="header">
        
    <form class="flex items-center" action="{{ route('books.search')}}">   
        <div class=" bg-white relative w-full">
            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
            </div>
            <input name="q" type="text" id="simple-search" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Title" required>
        </div>
        <button type="submit" class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span class="sr-only">Search</span>
        </button>
    </form>

    </x-slot>
    <div class="flex justify-center w-full flex-wrap">
    <div class="flex justify-around flex-wrap w-11/12">
    @isset($book_list)
    @foreach($book_list as $book)
    <div class="w-full lg:w-2/5 h-auto m-2 lg:m-5 bg-white border rounded flex justify-start flex-wrap">
        <div class="w-1/4"><img src="{{$book['cover_url'] }}" class="w-full h-auto"/></div>
        <div class ="p-3 w-3/4 flex flex-wrap flex-col justify-between">
            <div><h3 class="font-bold">{{ $book['title'] }}</h3>
            <p>
                @foreach($book['authors'] as $key => $author)
                @if($key > 0)  
                    {{ ', ' . $author }}
                @else 
                {{ $author }}
                @endif
                @endforeach
            </p></div>
            <div><a href="{{ route('books.show', ['id' => $book['isbn']]) }}"><button type="button" class="py-1 px-2 font-small bg-white-700 border border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white rounded-lg mt-2">Details</button></a></div>
        </div>

    </div>
    @endforeach
    @endisset
</div>
</div>

    <!--div class="py-12">
        <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
                @isset($book_list)
                <p>{{ $book_count }} Books found. </p>
                @foreach($book_list as $book)
                <div class="bg-white p-8 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <img src="{{ $book['cover_url'] }}" height="100" width="auto" />
                    <h2>{{ $book['title'] }}</h2>
                    <p>
                    @foreach($book['authors'] as $author )
                    Author: {{ $author }}           
                    @endforeach
                    </p> 
                    <p>Pages: {{ $book['pages'] }} </p>
                    <p>ISBN: {{ $book['isbn'] }} </p>
                </div>
                @endforeach
                @else 
                <div class="bg-white p-8 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <p>Search for a book or add manually</p>
                </div>
                @endisset
        </div>
    </div-->
</x-app-layout>
