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
                                    @php 
                                        //Publishers from model has a name property, 
                                        //whereas publisher from API response does not have name property,
                                        //So check to avoid errors
                                        if($publisher instanceof App\Models\Publisher)
                                        {
                                            $publisher = $publisher->name;
                                        }
                                    @endphp

                                    @if($index > 0)
                                        {{ '/ ' . $publisher }}
                                    @else
                                        {{ $publisher }}
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
                                    @php 
                                        //Subjects from model has a name property, 
                                        //whereas subjects from API response does not have name property,
                                        //So check to avoid errors.
                                        if($subject instanceof App\Models\Subject)
                                        {
                                            $subject = $subject->name;
                                        }
                                    @endphp
                                    
                                    @if($index >= 3) 
                                        @break
                                    @endif
                                    <span class="inline-block bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900 mt-5">
                                        {{$subject}}
                                    </span>
                                @endforeach
                            </div>  
                        @endif

                        @if($type == "MODEL_DATA")
                            <form id="read_pages_update_form">
                                <label class="mr-2">Pages read: </label>
                                <input name="csrf_token" value="{{csrf_token()}}" type="hidden"/>
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
                                <button id="submit_read_pages_btn" type="submit" class="hidden"></button>
                            </form>
                        @endif
                    </div>

                    {{--Action buttons--}}
                    <div class="mt-10">
                        @if($type == 'SEARCH_DATA')
                            <form action="{{ route('books.create_with_data', ['edition_key' => $book->edition_key]) }}">
                                @csrf
                                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded mr-2">
                                    Add Book to My Library
                                </button>
                            </form>
                        @elseif($type == 'MODEL_DATA') 
                            <button onclick="submitUpdateReadPagesForm()" id="update_read_pages_btn"
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
        <div id="delete_popup_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >
            <p class="text-center">Are you sure you want to delete this book?</p>

            <div class="flex flex-row justify-center mt-5">
                <form action="{{ route('books.delete', ['id' => $book->id]) }}" method="post">
                    @csrf 
                    @method('PATCH')
                        <button type="submit" class="bg-red-700 border border-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded mr-2">
                            Yes, Delete
                        </button>
                </form>
                <button onclick="closePopupBox('delete_popup_box')" 
                        class="bg-white border border-blue-800 text-blue-800 hover:bg-blue-800 hover:text-white font-bold py-1 px-3 rounded mr-2"> 
                    Cancel
                </button>
            </div>
        </div>
    @endif

    <div id="update_read_pages_success_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >
        <p class="text-center">Pages read has been successfully updated.</p>
        <div class="flex flex-row justify-center mt-5">
            <button onclick="closePopupBox('update_read_pages_success_box')" 
                    class="bg-white border border-blue-800 text-blue-800 hover:bg-blue-800 hover:text-white font-bold py-1 px-3 rounded mr-2"> 
                Ok
            </button>
        </div>
    </div>



    @if($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                alert('{{ implode(' ', $errors->all()) }}')
            });
           
        </script>
    @endif
    
</x-app-layout>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

<script>
$("#submit_read_pages_btn").click(function(e) {
    e.preventDefault();
    let read_pages = $("input[name=read_pages]").val();
    let book_id = $("input[name=book_id]").val();
    let csrf_token = $("input[name=csrf_token]").val();

    $.ajax({
        type: 'PUT',
        url: "{{ route('books.update_read_pages') }}",
        data: {
            "_token": csrf_token,
            read_pages:read_pages, 
            book_id:book_id 
        },
        success:function(data) {
            showUpdateReadPageSuccessMsg();
        }
    });
});
</script>

<script src="/js/showViewHandler.js"></script>
