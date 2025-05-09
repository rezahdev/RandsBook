<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-between flex-wrap w-11/12 md:w-4/5 mb-10" id="container">
            @if(isset($book_list) && count($book_list) > 0)
                <div class="w-full mt-3 p-1 rounded-xl flex flex-row justify-between">
                    <p class="md:text-lg">{{$num_book_found . ' in library.'}}</p>
                    <div class="flex flex-row justify-end border border-gray-300 px-4 py-1 rounded-xl ">
                        <img id="filter_img" class="pb-0.5 lg:scale-110 cursor-pointer w-7 border-r border-gray-300 pr-2" 
                                src="/resources/filter.png"
                                onclick="invokeFilterOptionsBox()">
                        <img id="sort_img" class="md:ml-5 pb-0.5 hover:scale-110 cursor-pointer w-5 ml-2" 
                                src="/resources/sort.png"
                                onclick="invokeSortOptionsBox()">
                    </div>
                </div>
                @foreach($book_list as $book)
                    @php 
                        $progress = round(($book->read_pages / $book->total_pages) * 100);
                    @endphp
                    <div class="w-full lg:w-[calc(50%-2rem)] h-auto my-2 lg:my-5 bg-white border rounded flex justify-start flex-wrap">
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
                            
                            <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 my-2">
                                <div class="bg-green-600 text-xs font-medium text-green-100 text-center p-0.5 leading-none rounded-full" 
                                     style="width: {{ $progress . '%' }}"> 
                                     {{ $progress . '%' }} 
                                </div>
                            </div>

                            {{--Link to see show book details--}}
                            <div>
                                <a href="{{ route('books.show_from_model', ['id' => $book->id]) }}"
                                   class="text-indigo-600 text-sm md:text-base md:font-semibold hover:text-blue-600">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
        
            @elseif($filtered_by == "completed")
                {{--Shown when no book is found in the DB for a given filter--}}
                <div class="w-full bg-white py-5 text-center my-10">
                    <p class="mb-5">You do not have any completed book in your library yet.</p>
                </div>
            elseif($filtered_by == "inprogress")
                {{--Shown when no book is found in the DB for a given filter--}}
                <div class="w-full bg-white py-5 text-center my-10">
                    <p class="mb-5">You do not have any in-progress book in your library yet.</p>
                </div>
            @else
                {{--Shown when no book is found in the DB--}}
                <div class="w-full bg-white pt-4 pb-10 text-center my-10">
                    <p class="mb-5">You do not have any book in your library yet.</p>
                    <a href="{{ route('books.search') }}"
                       class="text-blue-800 border border-blue-800 rounded pt-2 pb-3 px-3 hover:bg-blue-800 hover:text-white">
                        Click here to add new book 
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div id="filter_options_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >
        @php 
            $sortParam= '';
            if(request()->get('sort') && request()->get('order'))
            {
                $sortParam = '&sort=' . request()->sort . '&order=' . request()->order;
            }
        @endphp
        <ul class="text-center text-gray-600">Filter by
            <a href="\?filter=completed{{$sortParam}}">
                <li class="mt-3 text-indigo-700 cursor-pointer hover:font-semibold">
                    Completed books only
                </li>
            </a>
            <a href="\?filter=progress{{$sortParam}}">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    In-progress books only
                </li>
            </a>
            @php
                $href = '/';
                if(strlen($sortParam) > 0) { $href .= '?' . $sortParam; }
            @endphp
            <a href="{{$href}}">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Clear all filter
                </li>
            </a>
            <li class="text-indigo-700 text-center mt-3 cursor-pointer hover:font-semibold" 
                onclick="invokeFilterOptionsBox()">
                Cancel
            </li>
        </ul>
    </div>

    <div id="sort_options_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >
        @php 
            $url = '\?';
            if(request()->get('filter'))
            {
                $url .= 'filter=' . request()->filter . '&';
            }
        @endphp
        <ul class="text-center text-gray-600">Sort by
            <a href="{{$url}}sort=date_added&order=desc">
                <li class="mt-3 text-indigo-700 cursor-pointer hover:font-semibold">
                    Date added - recent to old
                </li>
            </a>
            <a href="{{$url}}sort=date_added&order=asc">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Date added - old to recent
                </li>
            </a>
            <a href="{{$url}}sort=progress&order=asc">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Progress - low to high
                </li>
            </a>
            <a href="{{$url}}sort=progress&order=desc">
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
window.onscroll = function ()  {
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200)  {
        scroll_to_top.classList.remove("hidden");
    } 
    else {
        scroll_to_top.classList.add("hidden");
    }
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function invokeFilterOptionsBox() {
    if(filter_options_box.style.visibility == "visible") {
        filter_options_box.style.visibility = "hidden";
    }
    else {
        //close the sort options box if it is open before opening filter box
        if(sort_options_box.style.visibility == "visible") {
            sort_options_box.style.visibility = "hidden";
        }
        const right = Math.round((innerWidth - container.offsetWidth)/2);
        filter_options_box.style.right = right + 'px';
        filter_options_box.style.visibility = "visible";
    }
}

function invokeSortOptionsBox() {
    if(sort_options_box.style.visibility == "visible") {
        sort_options_box.style.visibility = "hidden";
    }
    else {
        //close the filter options box if it is open before opening sort box
        if(filter_options_box.style.visibility == "visible") {
            filter_options_box.style.visibility = "hidden";
        }
        const right = Math.round((innerWidth - container.offsetWidth)/2);
        sort_options_box.style.right = right + 'px';
        sort_options_box.style.visibility = "visible";
    }
}
</script>