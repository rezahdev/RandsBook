<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-11/12">
            @if(isset($review) && isset($book))
                <form class="w-full md:w-3/4 my-5 bg-white rounded-xl p-3" method="post" action="{{route('community.bookReview.update') }}">
                    @csrf
                    @method('PUT')
                    
                    {{--If server returns form validation error--}}
                    @if($errors->any())
                        <ul class="block w-full my-2 text-red-500" id="error_msg">
                            @foreach($errors->all() as $error)
                            <li> {{ $error }} </li>
                            @endforeach
                        </ul>
                    @endisset

                    <label>Selected Book</label>
                    <select class="w-full mb-5 mt-2" name="book_id" readonly>
                        <option value="{{$book->id}}" selected>{{$book->title}}</option>
                    </select>
                    <input type="hidden" name="review_id" value="{{$review->id}}">
                    <textarea class="w-full h-96 rounded mb-5 p-2 mt-2" name="review" placeholder="Write your review here">{{$review->review}}</textarea>

                    <button type="submit" class="block bg-blue-800 hover:bg-blue-500 rounded text-white py-3 px-5 my-3">
                        Update
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>