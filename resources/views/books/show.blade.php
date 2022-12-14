<x-app-layout>
    <div class="w-full flex flex-wrap justify-center mt-5" id="main_content">
        @if($type == 'MODEL_DATA' || $type == 'SEARCH_DATA')
            <div class="w-11/12 flex flex-row flex-wrap justify-around">
                {{--Book image--}}
                <div class="w-full md:w-1/5 flex justify-center bg-white md:bg-transparent pt-3 md:pt-0 self-start">
                    <img src="{{ $book->cover_url }}" class="w-1/2 lg:w-full h-auto"/>
                </div>

                {{--Book info--}}
                <div class="w-full md:w-3/4 md:rounded flex flex-col flex-wrap justify-between">
                    <div class="bg-white w-full p-3">
                        <p class="font-bold">{{ $book->title }}</p>

                        @if(count($book->authors) > 0)
                            {{--Author info--}}
                            <p>Author:  
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
                            <p>Publisher: 
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
                            <p>Comment: {{ $book->public_comment }} </p>
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
                                    <span class="inline-block bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 pt-0.5 pb-1 rounded dark:bg-green-200 dark:text-green-900 mt-5">
                                        {{$subject}}
                                    </span>
                                @endforeach
                            </div>  
                        @endif

                        @if($type == "MODEL_DATA" && $book->isWishlistItem == '0')
                            
                            <label class="mr-2">Pages read: </label>
                            <input id="read_pages" name="read_pages" type="text" 
                                       value="{{$book->read_pages}}" 
                                       oldValue=""
                                       onfocus="this.oldValue = this.value"
                                       class="w-24 p-1 mt-5 rounded" 
                                       onkeydown="return event.key != 'Enter';"
                                       onchange="updateReadPagesRangerValue()">
                            <button id="update_read_pages_btn"
                                    class="bg-blue-700 hover:bg-green-700 text-white py-1 px-3 rounded mr-2 hidden"
                                    onclick="updateReadPages('{{$book->id}}', '{{csrf_token()}}')">
                                Update
                            </button>
                            <input id="read_pages_ranger" type="range" value="{{ $book->read_pages }}" 
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 my-5"
                                       min="0" max="{{$book->total_pages}}"
                                       onchange="updateReadPagesInputValue()">                     
                        @endif
                    </div>

                    {{--Action buttons--}}
                    <div class="bg-white p-3">
                        @if($type == 'SEARCH_DATA')
                            <form action="{{ route('books.create_with_data', ['edition_key' => $book->edition_key]) }}">
                                @csrf
                                <button type="submit" class="bg-blue-700 hover:bg-green-700 text-white py-1 px-3 rounded mr-2">
                                    Add to My Library
                                </button>
                            </form>
                        @elseif($type == 'MODEL_DATA' && $book->isWishlistItem == '0') 
                            <a href="{{ route('books.edit', ['id' => $book->id]) }}"
                               class="bg-blue-700 hover:bg-green-700 text-white px-3 rounded mr-2" style="padding-top: 6px; padding-bottom: 6px">
                                Edit Book
                            </a>
                            <button onclick="openDeletePopupBox()" class="bg-red-700 hover:bg-red-800 text-white py-1 px-3 rounded mr-2 mt-2">
                                Delete
                            </button >
                        @elseif($type == 'MODEL_DATA' && $book->isWishlistItem == '1') 
                            <button onclick="addToLibrary('{{$book->id}}', '{{csrf_token()}}')"
                                    class="bg-blue-700 hover:bg-green-700 text-white py-1 px-3 rounded mr-2" >
                                    Add to My Library
                            </button>
                            <button onclick="removeFromWishlist('{{$book->id}}', '{{csrf_token()}}')" 
                                    class="bg-red-700 hover:bg-red-800 text-white py-1 px-3 rounded mr-2 mt-2">
                                Remove from wishlist
                            </button >
                        @endif
                    </div> 
                    
                    <div class="w-full mb-24 mt-2">
                        <div class="w-full bg-white py-3 text-center mb-2 mt-4 font-semibold">
                            <p>
                                Reviews
                                @if(!empty($reviews))
                                {{ (count($reviews)) }}
                                @else
                                (0)
                                @endif
                            </p>
                        </div>
                        <div class="w-full mb-3">
                            @if(!empty($reviews))
                                @foreach($reviews as $review)
                                    <div class="mb-2 bg-white p-3">
                                        <div class="flex flex-row justify-between items-center">
                                            @if($review->use_nickname == 1)
                                                <p class="font-semibold">{{$review->user_nickname}}</p>
                                            @else 
                                                <p class="font-semibold">{{$review->user_name}}</p>
                                            @endif

                                            <p>{{explode(' ', $review->review_date)[0]}}</p>
                                        </div>
                                        <div>
                                            <p>{{$review->comment}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
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
                    @method('DELETE')
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
        <p class="text-center">{{--dynamically filled by js--}}</p>
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
<script src="https://cdn.jsdelivr.net/npm/party-js@latest/bundle/party.min.js"></script>
<script src="/js/showBookViewHandler.js"></script>
