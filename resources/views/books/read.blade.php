<x-app-layout>
<div class="row justify-content-center">
    <iframe src="/files/{{ $book_file_path }}">
            Your browser does not support PDFs.
    </iframe>
</div>
</x-app-layout>

<style>
    iframe {
        width: 80%;
        height: 90vh;
        margin-left: 10%;
    }
</style>