
<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-11/12">
            <form class="w-full md:w-3/4 my-5 bg-white rounded-xl p-3">
                <label>Title</label>
                <input class="w-full rounded mb-5 p-2" name="title" type="text" value="{{ $book['title'] }}">

                <label>Subtitle</label>
                <input class="w-full rounded mb-5 p-2" name="subtitle" type="text" value="{{ $book['subtitle'] }}">
                @php
                    $authors = "";
                    $publishers = "";
                    $subjects = "";

                    foreach($book['authors'] as $index => $author)
                    {
                        if($index > 0) $authors = $authors . ", ";
                        $authors = $authors . $author['name'];
                    }

                    foreach($book['publishers'] as $index => $publisher)
                    {
                        if($index > 0) $publishers = $publisher . ", ";
                        $publishers = $publishers . $publisher['name'];
                    }

                    foreach($book['subjects'] as $index => $subject)
                    {
                        if($index > 0) $subjects = $subjects . ", ";
                        $subjects = $subjects . $subject['name'];
                        if($index == 10) break;
                    }

                @endphp
                <label>Authors</label>
                <input class="w-full rounded mb-5 p-2" name="author" type="text" value="{{ $authors }}">

                <label>Publishers</label>
                <input class="w-full rounded mb-5 p-2" name="publishers" type="text" value="{{$publishers }}">

                <label>Publish Date</label>
                <input class="w-full rounded mb-5 p-2" name="publish_date" type="text" value="{{$book['publish_date'] }}">

                <label>Number of Pages</label>
                <input class="w-full rounded mb-5 p-2" name="total_pages" type="text" value="{{$book['total_pages'] }}">

                <label>Number of Pages Read</label>
                <input class="w-full rounded mb-5 p-2" name="read_pages" type="text" value="{{$book['read_pages'] }}">

                <label>Description</label>
                <textarea class="w-full rounded mb-5 p-2" name="description" value=" {{$book['description'] }}"></textarea>  

                <label>Subject Areas</label>
                <input class="w-full rounded mb-5 p-2" name="subjects" type="text" value="{{ $subjects }}">       

                <label>Comment (Only you can see this comment)</label>
                <input class="w-full rounded mb-5 p-2" name="comment" type="text" value="{{$book['comment'] }}">

                <label>Review (Other users can see this review)</label>
                <input class="w-full rounded mb-5 p-2" name="public_comment" type="text" value="{{ $book['public_comment'] }}">
 
                <button type="submit" class="block bg-blue-800 hover:bg-blue-500 rounded text-white py-3 px-5 my-3">Save book</button>
            </form>
        </div>
    </div>

</x-app-layout>
