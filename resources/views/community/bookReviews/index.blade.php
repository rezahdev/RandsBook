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
            @if(count($posts) > 0)
                @foreach($posts as $post)
                    <div class="w-full my-2 lg:my-5 bg-white border rounded flex justify-start flex-wrap">
                        {{--Book image--}}
                        <div class="w-48">
                            <img src="{{ $post->book->cover_url }}" class="w-full h-auto" />
                        </div>

                        <div class="p-2 pt-1 md:p-3 md:pt-3 w-3/4 flex flex-wrap flex-col justify-between">
                            {{--User and review--}}
                            <div>
                                <div>
                                    <p>{{$post->user}}</p>
                                    <p>{{$post->review_date}}
                                </div>
                                <h3 class="font-semibold md:font-bold text-sm md:text-base">{{ $post->book->title }}</h3>
                                {{--Authors--}}
                                <p class="text-sm md:text-base">
                                    @foreach($post->book->authors as $key => $author)
                                        @if($key > 0)
                                            {{ '/ ' . $author->name }}
                                        @else
                                            {{ $author->name }}
                                        @endif
                                    @endforeach
                                </p>
                                <p>{{$post->review}}</p>
                            </div>

                            {{--Link to see show book details--}}
                            <div>
                                <a href="{{ route('books.show_from_model', ['id' => $post->book->id]) }}"
                                   class="text-indigo-600 text-sm md:text-base md:font-semibold hover:text-blue-600">
                                    Read Full Review
                                </a>
                                <img src="/resources/heart_filled.png" width="24" />
                                <img src="/resources/heart_filled.png" width="24" />
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>
            @else
                <div class="w-full bg-white pt-4 pb-10 text-center my-10">
                    <p class="mb-5">No reviews found. You can review a book by clicking the link below.</p>
                    <a href="{{ route('books.search') }}"
                       class="text-blue-800 border border-blue-800 rounded pt-2 pb-3 px-3 hover:bg-blue-800 hover:text-white">
                        Click here to add new book 
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