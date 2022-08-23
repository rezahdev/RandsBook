<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-around flex-wrap w-11/12">
            <form class="w-full md:w-3/4 my-5 bg-white rounded-xl p-3" method="post" action="{{route('books.store') }}">
                @csrf

                {{--If server returns form validation error--}}
                @isset($error)
                    <p class="block w-full text-center my-2 text-red-500" id="error_msg">{{ $error }}</p>
                @endisset

                <input type="hidden" name="isbn" value="{{ $book->isbn }}">
                <input type="hidden" name="cover_url" value="{{ $book->cover_url }}" >

                <label>Title</label>
                <input class="w-full rounded mb-5 p-2" 
                       name="title" type="text" 
                       value="{{ $book->title }}"
                       onkeydown="return event.key != 'Enter';">

                <label>Subtitle</label>
                <input class="w-full rounded mb-5 p-2" 
                       name="subtitle" 
                       type="text" 
                       value="{{ $book->subtitle }}"
                       onkeydown="return event.key != 'Enter';">

                @php
                    $authorCount = 0;
                    $publisherCount = 0;
                    $subjectCount = 0;
                @endphp

                <div class="flex flex-row flex-wrap flex-start">
                    <div class="w-full md:w-1/2">
                        <label>Authors</label><br>

                        {{--Authors input field that can be dynamically added or removed--}}
                        <div id="author_list" class="w-full">
                            @if(count($book->authors) > 0)
                                @foreach($book->authors as $author)
                                    <div id="{{ 'author' . ++$authorCount }}">
                                        <input class="authors rounded" 
                                               name="{{ 'author' . $authorCount }}" 
                                               type="text"
                                               value="{{ $author->name }}" 
                                               onkeydown="return event.key != 'Enter';">
                                        <button onclick="deleteAuthorField('author{{ $authorCount }}')" 
                                                type="button"
                                                class="inline-flex items-center p-0.5 ml-2 text-sm text-blue-400 bg-transparent 
                                                       rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-300 
                                                       dark:hover:text-blue-900"
                                                data-dismiss-target="#badge-dismiss-default" 
                                                aria-label="Remove">

                                            <svg aria-hidden="true" 
                                                 class="w-3.5 h-3.5" 
                                                 aria-hidden="true" 
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20" 
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                      d="M4.293 4.293a1 1 0 011.414 
                                                        0L10 8.586l4.293-4.293a1 1 0 
                                                        111.414 1.414L11.414 10l4.293 
                                                        4.293a1 1 0 01-1.414 1.414L10 
                                                        11.414l-4.293 4.293a1 1 0 
                                                        01-1.414-1.414L8.586 10 4.293 
                                                        5.707a1 1 0 010-1.414z"
                                                      clip-rule="evenodd">
                                                </path>
                                            </svg>

                                            <span class="sr-only">Remove badge</span>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div id="{{ 'author' . ++$authorCount }}">
                                    <input onkeypress="inputAuthorFieldChanged(this)" 
                                        class="authors rounded"
                                        name="{{ 'author' . $authorCount }}" 
                                        type="text" value=""
                                        onkeydown="return event.key != 'Enter';">
                                    <button onclick="deleteAuthorField('author{{ $authorCount }}')" 
                                            type="button"
                                            class="inline-flex items-center p-0.5 ml-2 text-sm text-blue-400 bg-transparent 
                                            rounded-sm hover:bg-blue-200 hover:text-blue-900 dark:hover:bg-blue-300 dark:hover:text-blue-900"
                                            data-dismiss-target="#badge-dismiss-default" 
                                            aria-label="Remove">

                                        <svg aria-hidden="true" 
                                            class="w-3.5 h-3.5" 
                                            aria-hidden="true" 
                                            fill="currentColor"
                                            viewBox="0 0 20 20" 
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 
                                                    1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 
                                                    1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 
                                                    10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd">
                                            </path>
                                        </svg>

                                        <span class="sr-only">Remove badge</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if(count($book->authors) > 0)
                            <p id="create_author_label" 
                               class="text-blue-800 cursor-pointer mb-5" 
                               style="margin-top:-10px"
                               onclick="createAuthorInputField('{{++$authorCount}}')">
                               Add Another Author
                            </p>
                        @else
                            <p id="create_author_label" 
                               class="text-blue-800 cursor-pointer mb-5"
                               style="margin-top:-10px; display: none"
                               onclick="createAuthorInputField('{{++$authorCount}}')">
                               Add Another Author
                            </p>
                        @endif

                        <label>Number of Pages</label>
                        <input class="w-full md:w-5/6 rounded mb-5 p-2" 
                               name="total_pages" type="text"
                               value="{{ $book->total_pages }}" 
                               onkeydown="return event.key != 'Enter';">

                        <br>
                        <label>Number of Pages Read</label>
                        <input class="w-full md:w-5/6 rounded mb-5 p-2" 
                               name="read_pages" type="text"
                               value="{{ $book->read_pages }}" 
                               onkeydown="return event.key != 'Enter';">
                    </div>

                    <div class="w-full md:w-1/2">
                        <label>Publishers</label><br>

                        {{--Publishers input field that can be dynamically added or removed--}}
                        <div id="publisher_list">
                            @if(count($book->publishers) > 0)
                                @foreach($book->publishers as $publisher)
                                    <div id="{{'publisher' . ++$publisherCount }}">
                                        <input class="publishers rounded" 
                                               name="{{ 'publisher' . $publisherCount }}" 
                                               type="text"
                                               value="{{ $publisher->name }}" 
                                               onkeydown="return event.key != 'Enter';">
                                        <button onclick="deletePublisherField('publisher{{$publisherCount}}')" 
                                                type="button"
                                                class="inline-flex items-center p-0.5 ml-2 text-sm 
                                                       text-blue-400 bg-transparent rounded-sm hover:bg-blue-200 
                                                       hover:text-blue-900 dark:hover:bg-blue-300 dark:hover:text-blue-900"
                                                data-dismiss-target="#badge-dismiss-default" aria-label="Remove">
                                            <svg aria-hidden="true" 
                                                 class="w-3.5 h-3.5" 
                                                 aria-hidden="true" 
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 
                                                      1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 
                                                      4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd">
                                                </path>
                                            </svg>
                                            <span class="sr-only">Remove badge</span>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div id="{{'publisher' . ++$publisherCount }}">
                                    <input onkeypress="inputPublisherFieldChanged(this)" 
                                           class="publishers rounded"
                                           name="{{ 'publisher' . $publisherCount }}" 
                                           type="text" value=""
                                           onkeydown="return event.key != 'Enter';">
                                    <button onclick="deletePublisherField('publisher{{$publisherCount}}')" 
                                            type="button"
                                            class="inline-flex items-center p-0.5 ml-2 text-sm text-blue-400 
                                                  bg-transparent rounded-sm hover:bg-blue-200 hover:text-blue-900 
                                                  dark:hover:bg-blue-300 dark:hover:text-blue-900"
                                            data-dismiss-target="#badge-dismiss-default" aria-label="Remove">
                                        <svg aria-hidden="true" 
                                             class="w-3.5 h-3.5" 
                                             aria-hidden="true" 
                                             fill="currentColor"
                                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 
                                                   1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 
                                                   1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 
                                                   10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd">
                                            </path>
                                        </svg>
                                        <span class="sr-only">Remove badge</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if(count($book->publishers) > 0)
                            <p  id="create_publisher_label" 
                                class="text-blue-800 cursor-pointer mb-5"
                                style="margin-top:-10px" 
                                onclick="createPublisherInputField('{{++$publisherCount}}')">
                                    Add Another Publisher
                            </p>
                        @else
                            <p id="create_publisher_label" 
                               class="text-blue-800 cursor-pointer mb-5"
                               style="margin-top:-10px; display:none"
                               onclick="createPublisherInputField('{{++$publisherCount}}')">
                               Add Another Publisher
                            </p>
                        @endif

                        <label>Publish Date</label>
                        <input class="w-full md:w-5/6 rounded mb-5 p-2" 
                               name="publish_date" 
                               type="text"
                               value="{{ $book->publish_date }}" 
                               onkeydown="return event.key != 'Enter';">
                    </div>
                </div>

                <label>Description</label>
                <textarea class="w-full rounded mb-5 p-2" name="description" value=" {{ $book->description }}"></textarea>

                <label>Subject Tags</label><br>
                <div class="flex flex-row justify-start flex-wrap mb-5">
                    <div class="flex flex-row justify-start w-full md:w-5/12 md:mr-5">
                        <input id="subject_input_field" 
                               class="w-10/12 h-10 rounded mb-5 mr-5 p-2" 
                               name="subject"
                               type="text" 
                               onkeydown="return event.key != 'Enter';">
                        <p class="w-14 h-10 border-blue-800 bg-blue-800 rounded p-2 cursor-pointer text-center text-white hover:bg-blue-700"
                           id="add_subject_tag_label"
                           onclick="addSubjectTag('{{ ++$subjectCount }}')">
                           Add
                        </p>
                    </div>

                    <div id="subject_list" class="w-1/2">
                        <div style="display:none" class="inline" id="subject0">
                            <input type="hidden" name="subject0" value="">
                            <span id="badge-dismiss-green"
                                  class="mb-3 inline-flex items-center py-1 px-2 mr-2 text-sm font-medium 
                                       text-green-800 bg-green-100 rounded dark:bg-green-200 dark:text-green-800">
                                <span></span>
                                <button onclick="deleteSubjectTag('subject0')" 
                                        type="button"
                                        class="inline-flex items-center p-0.5 ml-2 text-sm text-green-400 
                                        bg-transparent rounded-sm hover:bg-green-200 hover:text-green-900 
                                        dark:hover:bg-green-300 dark:hover:text-green-900"
                                        data-dismiss-target="#badge-dismiss-green" 
                                        aria-label="Remove">
                                    <svg aria-hidden="true" 
                                         class="w-3.5 h-3.5" 
                                         aria-hidden="true" 
                                         fill="currentColor"
                                         viewBox="0 0 20 20" 
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 
                                            1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 
                                            4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd">
                                        </path>
                                    </svg>
                                    <span class="sr-only">Remove badge</span>
                                </button>
                            </span>
                        </div>
                        @foreach($book->subjects as $index => $subject)
                            @if($index < 3) 
                                <div class="inline" id="{{'subject' . ++$subjectCount }}">
                                    <input type="hidden" name="{{'subject' . $subjectCount}}" value="{{ $subject->name }}">
                                    <span id="badge-dismiss-green"
                                        class="mb-3 inline-flex items-center py-1 px-2 mr-2 text-sm font-medium 
                                              text-green-800 bg-green-100 rounded dark:bg-green-200 dark:text-green-800">
                                        <span>{{ $subject->name }}</span>
                                        <button onclick="deleteSubjectTag('subject{{$subjectCount}}')"
                                                type="button"
                                                class="inline-flex items-center p-0.5 ml-2 text-sm text-green-400 
                                                    bg-transparent rounded-sm hover:bg-green-200 hover:text-green-900 
                                                    dark:hover:bg-green-300 dark:hover:text-green-900"
                                                data-dismiss-target="#badge-dismiss-green" aria-label="Remove">
                                            <svg aria-hidden="true" 
                                                 class="w-3.5 h-3.5" 
                                                 aria-hidden="true" 
                                                 fill="currentColor"
                                                 viewBox="0 0 20 20" 
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 
                                                        1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 
                                                        4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                      clip-rule="evenodd">
                                                </path>
                                            </svg>
                                            <span class="sr-only">Remove badge</span>
                                        </button>
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <label>Comment (Only you can see this comment)</label>
                <textarea class="w-full rounded mb-5 p-2" name="comment" value="{{$book->comment }}"></textarea>

                <label>Review (Other users can see this review)</label>
                <textarea class="w-full rounded mb-5 p-2" name="public_comment" value="{{ $book->public_comment }}"></textarea>

                <button type="submit" class="block bg-blue-800 hover:bg-blue-500 rounded text-white py-3 px-5 my-3">
                    Save book
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
<script src="/js/createFormHandler.js"></script>