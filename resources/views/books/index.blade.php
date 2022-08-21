<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-full md:w-10/12">
            @isset($book_list)
            <p class="w-full text-center mt-3 p-1 rounded-xl">{{count($book_list) . ' books found.'}}</p>
            @foreach($book_list as $book)
            <div class="w-full lg:w-2/5 h-auto m-2 lg:m-5 bg-white border rounded flex justify-start flex-wrap">
                <div class="w-1/4"><img src="{{$book['cover_url'] }}" class="w-full h-auto" /></div>
                <div class="p-3 w-3/4 flex flex-wrap flex-col justify-between">
                    <div>
                        <h3 class="font-semibold text-sm md:text-normal">{{ $book['title'] }}</h3>
                        <p class="text-sm md:text-normal">
                            @foreach($book['authors'] as $key => $author)
                            @if($key > 0)
                            {{ ', ' . $author['name'] }}
                            @else
                            {{ $author['name'] }}
                            @endif
                            @endforeach
                        </p>
                    </div>
                    <div>
                       <a href="{{ route('books.show_from_model', ['id' => $book['id']]) }}"
                            class="text-indigo-600 font-large font-semibold hover:text-blue-600">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="bg-white p-5 text-center my-10">
                <p class="mb-5">Use the search box to find a book by name or author. You can also add the book
                    information manually if you cannot find the book. </p>
                <a href="{{ route('books.create') }}"
                    class="text-blue-500 font-semibold text-lg border border-blue-500 rounded py-2 px-3 hover:bg-blue-500 hover:text-white">
                    Click here to add manually </a>
            </div>
            @endisset
        </div>
    </div>

</x-app-layout>