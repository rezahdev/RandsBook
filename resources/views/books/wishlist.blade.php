<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        @if(count($book_list) > 0)
            <div class="flex justify-between flex-wrap w-11/12 md:w-4/5">
                <div class="w-full mt-3 p-1 rounded-xl flex flex-row justify-between">
                    <p class="md:text-lg">{{$num_book_found . ' in wishlist.'}}</p>
                </div>
                @foreach($book_list as $book_num => $book)
                    @php 
                        $progress = round(($book->read_pages / $book->total_pages) * 100);
                    @endphp
                    <div class="w-full lg:w-[calc(50%-2rem)] h-auto my-2 lg:my-5 bg-white border rounded flex justify-start flex-wrap"
                         id="wishlist_book_{{$book_num}}">
                        {{--Book image--}}
                        <div class="w-1/4">
                            <img src="{{$book->cover_url }}" class="w-full h-auto" />
                        </div>

                        <div class="p-2 pt-1 md:p-3 md:pt-3 w-3/4 flex flex-wrap flex-col justify-between">
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
                            <div class="flex flex-wrap flex-row justify-between">
                                <a href="{{ route('books.show_from_model', ['id' => $book->id]) }}"
                                   class="text-indigo-600 text-sm md:text-base md:font-semibold hover:text-blue-600">
                                    View Details
                                </a>
                                <img src="/resources/heart_filled.png" height="24" width="24" 
                                     class="cursor-pointer hover:scale-110"
                                     onclick="removeFromWishlist('wishlist_book_{{$book_num}}', '{{$book->id}}')" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{--Shown when no book is found in the DB--}}
            <div class="w-11/12 md:w-2/5 bg-white p-5 text-center my-10">
                <p class="mb-5">You do not have any book in your wishlist yet.</p>
                <a href="{{ route('books.search') }}"
                   class="text-blue-800 font-semibold text-lg border border-blue-800 rounded py-2 px-3 hover:bg-blue-800 hover:text-white">
                    Click here to search for books. 
                </a>
            </div>
        @endif
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

function removeFromWishlist(wishlistBookId, bookId)
{
    let http = new XMLHttpRequest();
    let url = "{{route('books.remove_from_wishlist')}}";
    let formData = new FormData();

    formData.append('book_id', bookId);
    formData.append('_token', '{{csrf_token()}}');
    formData.append('_method', 'DELETE');

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                let book = document.getElementById(wishlistBookId);
                book.parentNode.removeChild(book);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}
</script>