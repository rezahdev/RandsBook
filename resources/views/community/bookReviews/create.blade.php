<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-11/12">
            @if(isset($books) && count($books) > 0)
                <form class="w-full md:w-3/4 my-5 bg-white rounded-xl p-3" method="post" action="{{route('community.bookReview.store') }}">
                    @csrf

                    {{--If server returns form validation error--}}
                    @if($errors->any())
                        <ul class="block w-full my-2 text-red-500" id="error_msg">
                            @foreach($errors->all() as $error)
                            <li> {{ $error }} </li>
                            @endforeach
                        </ul>
                    @endisset

                    <label>Select Book</label>
                    <select class="w-full mb-5 mt-2" name="book_id">
                        @foreach($books as $book)
                            <option value="{{$book->id}}">{{$book->title}}</option>
                        @endforeach
                    </select>

                    <textarea class="w-full h-96 rounded mb-5 p-2 mt-2" name="review" placeholder="Write your review here"></textarea>

                    <button type="submit" class="block bg-blue-800 hover:bg-green-700 rounded text-white py-3 px-5 my-3">
                        Post
                    </button>
                </form>
            @else
                <script>
                    alert('You do not have any book in library. First add a book to write review.');
                    window.location.replace('/search');
                </script>
            @endif
        </div>
    </div>
</x-app-layout>