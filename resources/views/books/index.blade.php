<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-full md:w-10/12">

            @isset($book_list)
                <p class="w-full text-center mt-3 p-1 rounded-xl">{{count($book_list) . ' books found.'}}</p>

                @foreach($book_list as $book)
                    <div class="w-full lg:w-2/5 h-auto m-2 lg:m-5 bg-white border rounded flex justify-start flex-wrap">
                        {{--Book image--}}
                        <div class="w-1/4">
                            <img src="{{$book->cover_url }}" class="w-full h-auto" />
                        </div>

                        <div class="p-3 w-3/4 flex flex-wrap flex-col justify-between">
                            {{--Book information--}}
                            <div>
                                <h3 class="font-semibold md:font-bold text-sm md:text-base">{{ $book->title }}</h3>

                                {{--Authors--}}
                                <p class="text-sm md:text-base">
                                    @foreach($book->authors as $key => $author)
                                        @if($key > 0)
                                            {{ '/ ' . $author->name }}
                                        @else
                                            {{ $author->name }}
                                        @endif
                                    @endforeach
                                </p>
                            </div>

                            {{--Link to see show book details--}}
                            <div>
                                <a href="{{ route('books.show_from_model', ['id' => $book->id]) }}"
                                   class="text-indigo-600 font-large font-semibold hover:text-blue-600">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{--Shown when no book is found in the DB--}}
                <div class="bg-white p-5 text-center my-10">
                    <p class="mb-5">You do not have any book in your library yet.</p>
                    <a href="{{ route('books.search') }}"
                       class="text-blue-800 font-semibold text-lg border border-blue-800 rounded py-2 px-3 hover:bg-blue-800 hover:text-white">
                        Click here to add new book </a>
                </div>
            @endisset
        </div>
    </div>
    <button id="scroll_to_top" onclick="scrollToTop()"
            class="hidden fixed z-90 bottom-8 right-8 border-0 w-12 h-12 md:w-16 md:h-16 
            rounded-full drop-shadow-md bg-indigo-500 text-white text-3xl font-bold">
            &uarr;
    </button>
</x-app-layout>

<script>
window.onscroll = function () 
{
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) 
    {
        scroll_to_top.classList.remove("hidden");
    } 
    else 
    {
        scroll_to_top.classList.add("hidden");
    }
}
function scrollToTop() 
{
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>