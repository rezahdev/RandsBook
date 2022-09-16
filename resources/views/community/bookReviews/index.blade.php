<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="w-11/12 md:w-4/5 mb-10" id="container">
            @if(isset($reviews) && count($reviews) > 0)
                <div class="w-full mt-3 p-1 rounded-xl flex flex-row justify-between">
                    @if(!$isFilteredResult)
                        <div class="flex flex-row justify-end border border-gray-300 rounded px-2 py-0.5 hover:scale-110">
                            <img id="sort_img" class="pb-0.5 hover:scale-110 cursor-pointer w-5" 
                                    src="/resources/sort.png"
                                    onclick="invokeSortOptionsBox()">
                        </div>
                    @endif
                    <div>
                        <a href="{{route('community.bookReview.create')}}" 
                        class="text-blue-700 font-semibold hover:text-blue-800">
                        Write A New Review
                        </a>
                    </div>
                </div>

                @foreach($reviews as $review)
                    <div class="w-full mb-2 mt-1 md:mb-5 md:mt-2 bg-white border rounded flex justify-start flex-wrap" id="review{{$review->id}}">
                        {{--Book image--}}
                        <div class="w-24 md:w-36">
                            <img src="{{ $review->book->cover_url }}" class="w-full h-auto" />
                        </div>

                        <div class="p-2 pt-1 md:p-3 md:pt-2 w-[calc(100%-6rem)] md:w-[calc(100%-9rem)] flex flex-wrap flex-col justify-between">
                            {{--User and review--}}
                            <div>
                                <div class="w-full flex flex-row flex-wrap justify-between">
                                    @if($review->user->use_nickname == '1')
                                        <p>{{$review->user->nickname}}</p>
                                    @else 
                                        <p>{{$review->user->name}}</p>
                                    @endif

                                    <p>{{explode(' ', $review->created_at)[0]}}</p>
                                </div>

                                <h3 class="font-semibold md:font-bold text-sm md:text-base mt-2">{{ $review->book->title }}</h3>
              
                                {{--Authors--}}
                                <p class="text-sm md:text-base">Author: 
                                    @foreach($review->book->authors as $key => $author)
                                        @if($key > 0)
                                            {{ '/ ' . $author->name }}
                                        @else
                                            {{ $author->name }}
                                        @endif
                                    @endforeach
                                </p>
                                <p id="reviewText{{$review->id}}" class="mt-2">{{$review->reviewPreview}}</p>
                            </div>

                            {{--Link to see show book details--}}
                            <div class="w-full flex flex-row flex-wrap justify-between mt-2">
                                <p  onclick="showFullReview(this, 'hideFullReviewBtn{{$review->id}}', 'reviewText{{$review->id}}', '{{json_encode($review->review)}}')"
                                    class="text-indigo-600 font-semibold hover:font-bold cursor-pointer"
                                    id="showFullReviewBtn{{$review->id}}">
                                    Show More
                                </p>
                                <p  onclick="hideFullReview(this, 'showFullReviewBtn{{$review->id}}', 'reviewText{{$review->id}}', '{{json_encode($review->reviewPreview)}}')"
                                    class="text-indigo-600 font-semibold hover:font-bold cursor-pointer"
                                    id="hideFullReviewBtn{{$review->id}}"
                                    style="display:none">
                                    Show Less
                                </p>
                                <div class="flex flex-row flex-wrap justify-end items-center">
                                    @if($review->isLikedByThisUser)
                                        <button class="flex items-center text-sm hover:scale-105 border 
                                                      border-gray-300 rounded-xl px-2"
                                                style="padding-top: 1px; padding-bottom: 1px" 
                                                onclick="unlikeReview(this, '{{$review->id}}', '{{csrf_token()}}')">
                                            <img src="/resources/like_filled.png" 
                                                 class="w-4 md:w-5 inline mr-1 border-r border-gray-300" />
                                            <span>{{$review->likeCount}}</span>
                                        </button>
                                    @else
                                        <button class="flex items-center text-sm hover:scale-105 border 
                                                       border-gray-300 rounded-xl px-2"
                                                style="margin-top: 1px; padding-bottom: 1px" 
                                                onclick="likeReview(this, '{{$review->id}}', '{{csrf_token()}}')">
                                            <img src="/resources/like_blank.png" 
                                                 class="w-4 md:w-5 inline mr-1 border-r border-gray-300 mr-1" />
                                            <span>{{$review->likeCount}}</span>
                                        </button>
                                    @endif

                                    @if($review->isSavedByThisUser && !$review->isReviewdByThisUser)
                                        <button onclick="unsaveReview(this, '{{$review->id}}', '{{csrf_token()}}')"  
                                                class="ml-2 md:ml-5 hover:scale-105">
                                            <img src="/resources/save_filled.png" class="w-5 md:w-6" />
                                        </button>
                                    @elseif(!$review->isReviewdByThisUser)
                                        <button onclick="saveReview(this, '{{$review->id}}', '{{csrf_token()}}')"  
                                                class="ml-2 md:ml-5 hover:scale-105">
                                            <img src="/resources/save_blank.png" class="w-5 md:w-6" />
                                        </button>
                                    @endif

                                    @if($review->isReviewdByThisUser)
                                        <button class="ml-2 md:ml-5 hover:scale-105">
                                            <a href="{{route('community.bookReview.edit', ['id' => $review->id])}}">
                                                <img src="/resources/edit.png" class="w-5 md:w-6"/>
                                            </a>
                                        </button>
            
                                        <button onclick="openDeletePopupBox('{{$review->id}}')"  class="ml-2 md:ml-5 hover:scale-105">
                                            <img src="/resources/delete.png" class="w-5 md:w-6"/>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="w-full bg-white pt-4 pb-10 text-center mb-5 mt-2">
                    <p class="mb-5">No reviews found! You can contribute to the community by
                        reviewing a book from your library.</p>
                    <a href="{{ route('community.bookReview.create') }}"
                       class="text-blue-800 border border-blue-800 rounded pt-2 pb-3 px-3 hover:bg-blue-800 hover:text-white">
                        Click here to review a book
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div id="sort_options_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >       
        <ul class="text-center text-gray-600">Sort by
            <a href="/community/bookReviews/">
                <li class="mt-3 text-indigo-700 cursor-pointer hover:font-semibold">
                    Most Recent
                </li>
            </a>
            <a href="/community/bookReviews/?sort=like">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Most Liked
                </li>
            </a>
            <a href="/community/bookReviews/?sort=saved">
                <li class="text-indigo-700 cursor-pointer hover:font-semibold">
                    Most Saved
                </li>
            </a>
            <li class="text-indigo-700 cursor-pointer text-center mt-3 hover:font-semibold" 
                onclick="invokeSortOptionsBox()">
                Cancel
            </li>
        </ul>
    </div>

    <div id="delete_popup_box" class="fixed w-11/12 md:w-1/2 bg-white p-5 rounded" >
        <p class="text-center">Are you sure you want to delete this review?</p>
        <p class="hidden" id="review_id_to_delete"></p>
        <div class="flex flex-row justify-center mt-5">
                    <button class="bg-red-700 border border-red-700 hover:bg-red-800 
                                   text-white font-bold py-2 px-4 rounded mr-2"
                            onclick="deleteReview('{{csrf_token()}}')">
                        Yes, Delete
                    </button>
                <button onclick="closeDeletePopupBox()" 
                       class="bg-white border border-blue-800 text-blue-800 hover:bg-blue-800 
                              hover:text-white font-bold py-1 px-3 rounded mr-2"> 
                    Cancel
                </button>
        </div>
    </div>

    <button id="scroll_to_top" onclick="scrollToTop()"
            class="hidden fixed z-90 bottom-8 right-8 border-0 w-12 h-12 md:w-16 md:h-16 
            rounded-full drop-shadow-md bg-indigo-500 text-white text-3xl font-bold">
            &uarr;
    </button>
</x-app-layout>

<script src="/js/bookReviewViewHandler.js"></script>