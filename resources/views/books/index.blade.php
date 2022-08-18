<x-app-layout>
    <div class="py-12">
        <div class="w-11/12 mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                @foreach($books as $book)
                <div class="p-6 bg-white border-b border-gray-200 mb-6">
                    {{ $book->book_name }}
                </div>
               @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
