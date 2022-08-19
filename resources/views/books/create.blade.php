
<x-app-layout>
    <script src="/js/createFormHandler.js"></script>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-11/12">
            <form class="w-full md:w-3/4 my-5 bg-white rounded-xl p-3" method="post" action="{{route('books.store') }}">
                @csrf
                <input type="hidden" name="isbn" value="{{ $book['isbn'] }}">
                <label>Title</label>
                <input class="w-full rounded mb-5 p-2" name="title" type="text" value="{{ $book['title'] }}">

                <label>Subtitle</label>
                <input class="w-full rounded mb-5 p-2" name="subtitle" type="text" value="{{ $book['subtitle'] }}">
                @php
                    $authorCount = 0;
                    $publisherCount = 0;
                @endphp

                <div class="flex flex-row flex-wrap flex-start">
                    <div class="w-full md:w-1/2">
                        <label>Authors</label><br>               
                        <div id="author_list" class="w-full">
                            @foreach($book['authors'] as $author)
                            <div id="{{ 'author' . ++$authorCount }}">
                                <input class="authors rounded" name="{{ 'author' . $authorCount }}" type="text" value="{{ $author['name'] }}">
                                <button onclick="deleteAuthor('author{{ $authorCount }}')" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-blue-400 bg-transparent rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-300 dark:hover:text-blue-900" data-dismiss-target="#badge-dismiss-default" aria-label="Remove">
                                    <svg aria-hidden="true" class="w-3.5 h-3.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    <span class="sr-only">Remove badge</span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <p id="create_author_label" class="text-blue-800 cursor-pointer mb-5" style="margin-top:-10px" onclick="createAuthorInputField('{{++$authorCount}}')">Add Another Author</p>
                        
                        <label>Number of Pages</label>
                        <input class="w-5/6 rounded mb-5 p-2" name="total_pages" type="text" value="{{$book['total_pages'] }}">
                        <br>
                        <label>Number of Pages Read</label>
                        <input class="w-5/6 rounded mb-5 p-2" name="read_pages" type="text" value="{{$book['read_pages'] }}">
                    </div>
                    <div class="w-full md:w-1/2">
                        <div id="publisher_list">
                            <label>Publishers</label><br>
                            @foreach($book['publishers'] as $publisher)
                            <div id="{{'publisher' . ++$publisherCount }}">
                                <input class="publishers rounded" name="{{ 'publisher' . $publisherCount }}" type="text" value="{{$publisher['name'] }}">
                                <button onclick="deletePublisher('publisher{{$publisherCount+1}}')" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-blue-400 bg-transparent rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-300 dark:hover:text-blue-900" data-dismiss-target="#badge-dismiss-default" aria-label="Remove">
                                    <svg aria-hidden="true" class="w-3.5 h-3.5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    <span class="sr-only">Remove badge</span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <p id="create_publisher_label" class="text-blue-800 cursor-pointer mb-5" style="margin-top:-10px" onclick="createPublisherInputField('{{++$publisherCount}}')">Add Another Publisher</p>
                    
                        <label>Publish Date</label>
                        <input class="w-5/6 rounded mb-5 p-2" name="publish_date" type="text" value="{{$book['publish_date'] }}">
                    </div>
                </div>

                

                <div class="flex flex-row flex-wrap justify-around">
                    <div>
                        
                    </div>
                    <div>
                        
                    </div>
                </div>

                <label>Description</label>
                <textarea class="w-full rounded mb-5 p-2" name="description" value=" {{$book['description'] }}"></textarea>  
                
                {{--<label>Subject Areas</label>
                <input class="w-full rounded mb-5 p-2" name="subjects" type="text" value="{{ $subjects }}"> --}}      

                <label>Comment (Only you can see this comment)</label>
                <textarea class="w-full rounded mb-5 p-2" name="comment" type="text" value="{{$book['comment'] }}"></textarea>

                <label>Review (Other users can see this review)</label>
                <textarea class="w-full rounded mb-5 p-2" name="public_comment" type="text" value="{{ $book['public_comment'] }}"></textarea>
 
                <button type="submit" class="block bg-blue-800 hover:bg-blue-500 rounded text-white py-3 px-5 my-3">Save book</button>
            </form>
        </div>
    </div>

</x-app-layout>
