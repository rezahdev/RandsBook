<x-app-layout>
    <div class="w-full flex flex-wrap justify-center mt-10">
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
                <p>Number of Pages: {{ $book['pages'] }} </p>
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
                    <form action="{{ route('books.create_with_data') }}" method="post">
                        @csrf
                        <input name="book_data" type="hidden" value="{{json_encode($book)}}">
                        <button type="submit" class="bg-blue-800 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mr-2">
                            Edit and Add
                        </button>
                    </form>
                </div>     
            </div>
        </div>
        @else
        <div>
            <p>{{ $response }}</p>
        </div>
        @endisset
    </div>
    
</x-app-layout>
