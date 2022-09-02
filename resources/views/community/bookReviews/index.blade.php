<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="w-11/12 md:w-4/5 mb-10" id="container">
            <div class="w-full mt-3 p-1 rounded-xl flex flex-row justify-between">
                <div class="flex flex-row justify-end">
                    <img id="sort_img" class="ml-3 md:ml-5 pb-0.5 lg:scale-110 hover:scale-105 cursor-pointer" 
                             src="/resources/sort.png" width="32" height="32"
                             onclick="invokeSortOptionsBox()">
                </div>
            </div>
            @if(count($reviews) > 0)
                @foreach($reviews as $review)
                    <div class="w-full my-2 lg:my-5 bg-white border rounded flex justify-start flex-wrap">
                        {{--Book image--}}
                        <div class="w-24 md:w-36">
                            <img src="{{ $review->book->cover_url }}" class="w-full h-auto" />
                        </div>

                        <div class="p-2 pt-1 md:p-3 md:pt-2 w-[calc(100%-6rem)] md:w-[calc(100%-9rem)] flex flex-wrap flex-col justify-between">
                            {{--User and review--}}
                            <div>
                                <div class="w-full flex flex-row flex-wrap justify-between">
                                    @if($review->user->use_nickname == '1')
                                        <p>{{$review->user->nickname}}</p>
                                    @else 
                                        <p>{{$review->user->name}}</p>
                                    @endif

                                    <p>{{explode(' ', $review->created_at)[0]}}</p>
                                </div>

                                <h3 class="font-semibold md:font-bold text-sm md:text-base mt-2">{{ $review->book->title }}</h3>
              
                                {{--Authors--}}
                                <p class="text-sm md:text-base">Author: 
                                    @foreach($review->book->authors as $key => $author)
                                        @if($key > 0)
                                            {{ '/ ' . $author->name }}
                                        @else
                                            {{ $author->name }}
                                        @endif
                                    @endforeach
                                </p>
                                <p class="mt-2">{{$review->review}}</p>
                            </div>

                            {{--Link to see show book details--}}
                            <div class="w-full flex flex-row flex-wrap justify-between">
                                <a href="{{ route('books.show_from_model', ['id' => $review->book->id]) }}"
                                   class="text-indigo-600 text-sm md:text-base md:font-semibold hover:text-blue-600">
                                    Read Full Review
                                </a>
                                <div class="flex flex-row flex-wrap justify-end">
                                    <img src="/resources/heart_blank.png" width="24" />
                                    <img src="/resources/heart_filled.png" width="24" class="ml-5" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>
            @else
                <div class="w-full bg-white pt-4 pb-10 text-center my-10">
                    <p class="mb-5">No reviews found! You can contribute to the community by
                        reviewing a book from your library.</p>
                    <a href="{{ route('community.bookReview.create') }}"
                       class="text-blue-800 border border-blue-800 rounded pt-2 pb-3 px-3 hover:bg-blue-800 hover:text-white">
                        Click here to review a book
                    </a>
                </div>
            @endif
    </div>

    <div id="sort_options_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >       
        <ul class="text-center text-gray-600">Sort by
            <a href="/?sort=date_added&order=desc">
                <li class="mt-3 text-indigo-700 cursor-pointer hover:font-semibold">
                    Date added - recent to old
                </li>
            </a>
            <a href="/?sort=date_added&order=asc">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Date added - old to recent
                </li>
            </a>
            <a href="/?sort=progress&order=asc">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Progress - low to high
                </li>
            </a>
            <a href="/?sort=progress&order=desc">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Progress - high to low
                </li>
            </a>
            <li class="text-indigo-700 cursor-pointer text-center mt-3 hover:font-semibold" 
                onclick="invokeSortOptionsBox()">
                Cancel
            </li>
        </ul>
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

function invokeSortOptionsBox()
{
    if(sort_options_box.style.visibility == "visible")
    {
        sort_options_box.style.visibility = "hidden";
    }
    else
    {  
        const right = Math.round((innerWidth - container.offsetWidth)/2);
        sort_options_box.style.right = right + 'px';
        sort_options_box.style.visibility = "visible";
    }
}
</script>