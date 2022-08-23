<x-app-layout>
    <div class="w-full flex flex-wrap justify-center mt-10" id="main_content">
        @isset($book)
        <div class="w-11/12 flex flex-row flex-wrap justify-around">
            <div class="w-full md:w-1/5 flex justify-center bg-white md:bg-transparent pt-3 md:pt-0">
                <img src="{{ $book['cover_url'] }}" class="w-1/2 lg:w-full h-auto"/>
            </div>
            <div class="w-full md:w-3/4 p-3 bg-white md:rounded mb-10 pt-5">
                <p class="font-bold text-xl">{{ $book['title'] }}</p>
                <!p>Written by 
                    @foreach($book['authors'] as $index => $author)
                        @if($index > 0)
                            <span>/ </span>
                        @endif
                        <a class="text-blue-700" href="{{ $author['url'] }}">{{ trim($author['name']) }}</a>
                    @endforeach
                </p>
                <p>Publisher: 
                    @foreach($book['publishers'] as $index => $publisher)
                        @if($index > 0)
                            <span>, </span>
                        @endif
                        {{ $publisher['name'] }}
                    @endforeach
                </p>
                <p>Publish date: {{ $book['publish_date'] }} </p>
                <p>Number of Pages: {{ $book['total_pages'] }} </p>
                {{--<p>Description: {{ __($book['description']) }} </p> --}}
                <div class="mt-5 mb-5">
                    @foreach($book['subjects'] as $index => $subject)
                        @if($index >= 3) 
                            @break
                        @endif
                        <span class="inline-block bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900 mt-5">{{$subject['name']}}</span>
                    @endforeach
                </div>    
                <div class="mt-10">
                    @if($mode == "ADD")
                    <form action="{{ route('books.create_with_data') }}" method="post">
                        @csrf
                        <input name="book_data" type="hidden" value="{{json_encode($book)}}">
                        <button type="submit" class="bg-blue-800 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mr-2">
                            Add Book to My Library
                        </button>
                    </form>
                    @else 
                        <a href="{{ route('books.edit', ['id' => $book['id']]) }}"><button type="submit" class="bg-blue-800 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mr-2">
                            Edit
                        </button></a>
                        <button onclick="openDeletePopupBox()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded mr-2">
                            Delete
                        </button >
                    @endif
                </div>     
            </div>
        </div>
        @else
        <div>
            <p>{{ $response }}</p>
        </div>
        @endisset
    </div>
    <div id="delete_popup_box" class="fixed w-11/12 md:1/2 bg-white p-5 rounded-xl display-none" >
        <p class="text-center">Are you sure you want to delete this book?</p>
        <div class="flex flex-row justify-center mt-5">
            <form action="{{ route('books.delete', ['id' => $book['id']]) }}" method="post">
                @csrf 
                @method('PATCH')
                    <button type="submit" class="bg-red-700 border border-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded mr-2">
                        Yes, Delete
                    </button>
            </form>
            <button onclick="closeDeletePopupBox()" class="bg-white border border-blue-800 text-blue-800 hover:bg-blue-800 hover:text-white font-bold py-1 px-3 rounded mr-2"> Cancel</button>
        </div>
    </div>
    
</x-app-layout>

<script>
    function openDeletePopupBox()
    {
        delete_popup_box.style.visibility = "visible";

        if(!main_content.classList.contains("blurry"))
        main_content.classList.add("blurry");
    }

    function closeDeletePopupBox()
    {
        delete_popup_box.style.visibility = "hidden";

        if(main_content.classList.contains("blurry"))
        main_content.classList.remove("blurry");
    }
    </script>
