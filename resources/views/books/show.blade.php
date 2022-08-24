<x-app-layout>
    <div class="w-full flex flex-wrap justify-center mt-10" id="main_content">
        @if($type == 'MODEL_DATA' || $type == 'SEARCH_DATA')
            <div class="w-11/12 flex flex-row flex-wrap justify-around">
                {{--Book image--}}
                <div class="w-full md:w-1/5 flex justify-center bg-white md:bg-transparent pt-3 md:pt-0">
                    <img src="{{ $book->cover_url }}" class="w-1/2 lg:w-full h-auto"/>
                </div>

                {{--Book info--}}
                <div class="w-full md:w-3/4 p-3 bg-white md:rounded mb-10 pt-5 flex flex-col felx-wrap justify-between">
                    <div>
                        <p class="font-bold text-xl">{{ $book->title }}</p>

                        @if(count($book->authors) > 0)
                            {{--Author info--}}
                            <p>Written by:  
                                @foreach($book->authors as $index => $author)
                                    @if($index > 0)
                                        {{ '/ ' . $author->name }}
                                    @else
                                        {{ $author->name }}
                                    @endif
                                @endforeach
                            </p>
                        @endif

                        @if(count($book->publishers) > 0)
                            {{--Publisher info--}}
                            <p>Published by: 
                                @foreach($book->publishers as $index => $publisher)
                                    @if($index > 0)
                                        {{ '/ ' . $publisher->name }}
                                    @else
                                        {{ $publisher->name }}
                                    @endif
                                @endforeach
                            </p>
                        @endif

                        @if(strlen($book->publish_date) > 0)
                            <p>Publish date: {{ $book->publish_date }} </p>
                        @endif
                        
                        <p>Number of Pages: {{ $book->total_pages }} </p>

                        @if(strlen($book->description) > 0)
                            <p>Description: {{ $book->description }} </p>
                        @endif

                        @if(strlen($book->comment) > 0)
                            <p>Comment: {{ $book->comment }} </p>
                        @endif

                        @if(strlen($book->public_comment) > 0)
                            <p>Your public review: {{ $book->public_comment }} </p>
                        @endif

                        @if(count($book->subjects) > 0)
                            {{--Book subjects--}}
                            <div>
                                @foreach($book->subjects as $index => $subject)
                                    @if($index >= 3) 
                                        @break
                                    @endif
                                    <span class="inline-block bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900 mt-5">
                                        {{$subject->name}}
                                    </span>
                                @endforeach
                            </div>  
                        @endif

                        @if($type == "MODEL_DATA")
                            <form id="read_pages_update_form" action="{{ route('books.update_read_pages') }}" method="post">
                                @csrf
                                @method('PUT')
                                <label class="mr-2">Pages read: </label>
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <input id="read_pages" name="read_pages" type="text" 
                                       value="{{$book->read_pages}}" 
                                       oldValue = ""
                                       onfocus="this.oldValue = this.value"
                                       class="w-24 p-1 mt-5 rounded" 
                                       onkeydown="return event.key != 'Enter';"
                                       onchange="updateReadPagesRangerValue()">
                                <input id="read_pages_ranger" type="range" value="{{ $book->read_pages }}" 
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 my-5"
                                       min="0" max="{{$book->total_pages}}"
                                       onchange="updateReadPagesInputValue()">
                            </form>
                        @endif
                    </div>

                    {{--Action buttons--}}
                    <div class="mt-10">
                        @if($type == 'SEARCH_DATA')
                            <form action="{{ route('books.create_with_data', ['isbn' => $book->isbn]) }}">
                                @csrf
                                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded mr-2">
                                    Add Book to My Library
                                </button>
                            </form>
                        @elseif($type == 'MODEL_DATA') 
                            <button onclick="submitUpdateReadPagesForm(event)" id="update_read_pages_btn"
                                    class="bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2" disabled>
                                Update Read Pages
                            </button>
                            <a href="{{ route('books.edit', ['id' => $book->id]) }}"
                               class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded mr-2">
                                Edit Book
                            </a>
                            <button onclick="openDeletePopupBox()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded mr-2">
                                Delete
                            </button >
                        @endif
                    </div>     
                </div>
            </div>
        @else
            {{--Shows the message if no book found by the given parameters--}}
            <div>
                <p>{{ $response }}</p>
            </div>
        @endisset
    </div>

    {{--If this book is shown from model--}}
    @if($type=='MODEL_DATA')
        <div id="delete_popup_box" class="fixed w-11/12 md:1/2 bg-white p-5 rounded-xl display-none" >
            <p class="text-center">Are you sure you want to delete this book?</p>

            <div class="flex flex-row justify-center mt-5">
                <form action="{{ route('books.delete', ['id' => $book->id]) }}" method="post">
                    @csrf 
                    @method('PATCH')
                        <button type="submit" class="bg-red-700 border border-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded mr-2">
                            Yes, Delete
                        </button>
                </form>
                <button onclick="closeDeletePopupBox()" class="bg-white border border-blue-800 text-blue-800 hover:bg-blue-800 hover:text-white font-bold py-1 px-3 rounded mr-2"> Cancel</button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                alert('{{ implode(' ', $errors->all()) }}')
            });
           
        </script>
    @endif
    
</x-app-layout>

<script>
    function openDeletePopupBox()
    {
        delete_popup_box.style.visibility = "visible";

        if(!main_content.classList.contains("blurry"))
        {
            main_content.classList.add("blurry");
        }    
    }

    function closeDeletePopupBox()
    {
        delete_popup_box.style.visibility = "hidden";

        if(main_content.classList.contains("blurry"))
        {
            main_content.classList.remove("blurry");
        }
    }

    function updateReadPagesInputValue()
    {
        read_pages.value = read_pages_ranger.value;
        update_read_pages_btn.removeAttribute('disabled');
        update_read_pages_btn.classList.remove('bg-gray-700');
        update_read_pages_btn.classList.add('bg-blue-700');
        update_read_pages_btn.classList.add('hover:bg-blue-800');    
    }

    function updateReadPagesRangerValue()
    {
        if(!Number.isInteger(+read_pages.value))
        {
            alert('Pages read must be an integer');
            read_pages.value = read_pages.oldValue;
            return;
        }
        
        if(parseInt(read_pages.value) > parseInt(read_pages_ranger.max))
        {
            alert('Pages read cannot be greater than total pages.');
            read_pages.value = read_pages.oldValue;
            return;
        }

        read_pages_ranger.value = read_pages.value;
        update_read_pages_btn.removeAttribute('disabled');
        update_read_pages_btn.classList.remove('bg-gray-700');
        update_read_pages_btn.classList.add('bg-blue-700');
        update_read_pages_btn.classList.add('hover:bg-blue-800');
    }

    function submitUpdateReadPagesForm(event)
    {
        event.preventDefault();
        read_pages_update_form.submit();
    }


</script>
