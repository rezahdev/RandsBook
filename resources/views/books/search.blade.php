<x-app-layout>
    <x-slot name="header">
    <form method="get" action="{{ route('books.search') }}">   
    @csrf
    <label for="book_search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-gray-300">Search</label>
    <div class="relative">
        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
            <svg aria-hidden="true" class="w-5 h-5 text-black dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input name="q" type="search" id="book_search" class="block p-4 pl-10 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-400 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search by title, author, or subject..." required="">
        <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
    </div>
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
    @else
    <div class="bg-white p-5 text-center my-10">
        <p class="mb-5">use the search box to find a book by name or author. You can also add the book manually if you cannot find the book. </p> 
        <a href="#" class="text-blue-500 font-semibold text-lg border border-blue-500 rounded py-2 px-3 hover:bg-blue-500 hover:text-white"> Click here to add manually </a>
    </div>
    @endisset
</div>
</div>

</x-app-layout>
