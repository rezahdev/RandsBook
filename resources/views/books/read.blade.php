<x-app-layout>
<div class="row justify-content-center">
    <iframe src="{{'/../' . $book_file_path}}" width="50%" height="600">
            This browser does not support PDFs. Please download the PDF to view it: <a href="{{ asset('folder/file_name.pdf') }}">Download PDF</a>
    </iframe>
    <p>{{$book_file_path}}</p>
</div>
</x-app-layout>