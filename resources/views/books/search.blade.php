<x-app-layout>
    <x-slot name="header">
        <form method="get" id="search_form" action="{{ route('books.search') }}">
            @csrf
            <label for="book_search"
                   class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-gray-300">
                   Search
            </label>
            <div class="relative">
                <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                    <svg aria-hidden="true" 
                         class="w-5 h-5 text-black dark:text-black" 
                         fill="none" 
                         stroke="currentColor"
                         viewBox="0 0 24 24" 
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                        </path>
                    </svg>
                </div>
                <input name="q" type="search" id="book_search"
                    class="block p-4 pl-10 w-full text-sm text-gray-900 bg-white 
                          rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 
                          dark:bg-white dark:border-gray-600 dark:placeholder-gray-400 dark:text-black 
                          dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Search by title, author name..." 
                    required="">
                <button type="submit"
                        class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 
                          focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg 
                          text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Search
                </button>
            </div>
        </form>
    </x-slot>

    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-full lg:w-10/12">
            @if(isset($book_list) && isset($book_count))
                @if($book_count > 0)
                    <div class="w-full lg:w-11/12 mt-3 flex flex-row justify-between flex-wrap">
                        <p class="ml-2 text-center">{{$book_count . ' books found.'}}</p>         
                        <a href="{{ route('books.create') }}" 
                            class="text-blue-800 hover:font-semibold hover:text-blue-700 mr-2"> 
                            Click here to add manually 
                        </a>
                    </div>
                @else
                    <p class="pt-5 text-center">{{$book_count . ' books found.'}}</p>         
                    <div class="bg-white p-5 text-center mb-10 mt-5 w-11/12">
                        <p class="mb-5"> You can also add the book information manually if you cannot find the book. </p>
                        <a href="{{ route('books.create') }}"
                            class="text-blue-600 font-semibold text-lg border border-blue-600 rounded py-2 px-3 hover:bg-blue-700 hover:text-white">
                            Click here to add manually </a>
                    </div>  
                @endif

                @foreach($book_list as $book)
                    <div class="w-full lg:w-2/5 h-auto m-2 lg:m-5 bg-white border rounded flex justify-start flex-wrap">
                        <div class="w-1/4"><img src="{{$book->cover_url }}" class="w-full h-auto" /></div>
                        <div class="p-3 w-3/4 flex flex-wrap flex-col justify-between">
                            <div class="book_info">
                                <h3 class="font-semibold md:font-bold">{{ $book->title }}</h3>
                                <div>
                                    @if(count($book->author_name) > 0)
                                        <p class="text-sm md:text-base">Author:
                                            @foreach($book->author_name as $index => $author)
                                                @if($index > 0)
                                                    {{ '/ ' . $author }}
                                                @else
                                                    {{ $author }}
                                                @endif
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap flex-row justify-between">
                                <a href="{{ route('books.show_from_search_result', ['edition_key' => $book->edition_key]) }}"
                                    class="text-indigo-600 text-l font-bold hover:text-blue-600">
                                    View Details
                                </a>
                                @if($book->isWishlisted)
                                    <img src="/resources/heart_filled.png" height="24" width="24" 
                                         class="cursor-pointer hover:scale-110"
                                         onclick="removeFromWishlist(this, '{{$book->wishlistBookId}}')" />
                                @else
                                    <img src="/resources/heart_blank.png" height="24" width="24" 
                                         class="cursor-pointer hover:scale-110"
                                         onclick="addToWishlist(this, '{{$book->edition_key}}')" />
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach 
            @else
            <div class="bg-white p-5 text-center my-10 w-11/12">
                @isset($api_connect_error)
                    <p class="mb-5"> 
                        {{ $api_connect_error->message }}
                    </p>
                @else
                    <p class="mb-5">
                        Use the search box to find a book by title or author name. You can also add the book
                        information manually if you cannot find the book. 
                    </p>
                @endisset
                <a href="{{ route('books.create') }}"
                    class="text-blue-600 border border-blue-600 rounded py-2 px-3 hover:bg-blue-700 hover:text-white">
                    Click here to add manually 
                </a>
            </div>     
            @endif
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

function addToWishlist(wishlistBtnImg, editionKey)
{
    let http = new XMLHttpRequest();
    let url = "{{route('books.add_to_wishlist')}}";
    let csrfToken = '{{csrf_token()}}';
    let formData = new FormData();

    formData.append('edition_key', editionKey);
    formData.append('_token', csrfToken);

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                alert(responseObj.message);
                const img = wishlistBtnImg.cloneNode(true);
                img.removeAttribute('onclick');
                img.src = '/resources/heart_filled.png';
                img.addEventListener('click', function() { removeFromWishlist(img, responseObj.book_id, csrfToken) });
                wishlistBtnImg.parentNode.replaceChild(img, wishlistBtnImg);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function removeFromWishlist(wishlistBtnImg, bookId)
{
    let http = new XMLHttpRequest();
    let url = "{{route('books.remove_from_wishlist')}}";
    let csrfToken = '{{csrf_token()}}';
    let formData = new FormData();

    formData.append('book_id', bookId);
    formData.append('_token', csrfToken);
    formData.append('_method', 'DELETE');

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                const img = wishlistBtnImg.cloneNode(true);
                img.removeAttribute('onclick');
                img.src = '/resources/heart_blank.png';
                img.addEventListener('click', function() { addToWishlist(img, responseObj.edition_key, csrfToken) });
                wishlistBtnImg.parentNode.replaceChild(img, wishlistBtnImg);
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