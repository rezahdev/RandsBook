<x-app-layout>
    @if($errors->any())
        @if($errors->first() == 'The name field is required.')
            <script>
                document.addEventListener('DOMContentLoaded', function() { invokeUpdateNameBox() });           
            </script>
        @elseif($errors->first() == 'The nickname field is required.' || $errors->first() == 'The nickname has already been taken.')
            <script>
                document.addEventListener('DOMContentLoaded', function() { invokeUpdateNicknameBox() });
            </script>
        @else
            <script>
                document.addEventListener('DOMContentLoaded', function() { invokeUpdateEmailBox() });
            </script>
        @endif
    @endif
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-between flex-wrap w-11/12 md:w-4/5 mb-10" id="container">
            <div class="w-full flex flex-row flex-wrap">
                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:mr-1 rounded flex flex-row flex-wrap justify-between">
                    <p id="user_name">Name: {{$user->name}}</p>
                    <img src="/resources/pen.png" width="24" height="24" onclick="invokeUpdateNameBox()" 
                         class="cursor-pointer hover:scale-105">
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:ml-1 rounded flex flex-row flex-wrap justify-between">
                    <p id="user_email">Email: {{$user->email}} 
                        {{--@if(is_null($user->email_verified_at))
                            <span class="text-red-700">(Unverified)</span>
                        @else 
                            <span class="text-green-700">(Verified)</span>
                        @endif--}}
                    </p>
                    <img src="/resources/pen.png" width="24" height="24" onclick="invokeUpdateEmailBox()" 
                         class="cursor-pointer hover:scale-105">
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:mr-1 rounded flex flex-row flex-wrap justify-between">
                    @if($user->nickname != null)
                        <p id="user_name">Nickname: {{$user->nickname}}</p>
                    @else
                        <p id="user_name">Nickname: <span class="text-gray-700">Not set yet</span></p>
                    @endif
                    <img src="/resources/pen.png" width="24" height="24" onclick="invokeUpdateNicknameBox()" 
                         class="cursor-pointer hover:scale-105">
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:ml-1 rounded flex flex-row flex-wrap justify-between">
                    <a href="{{route('profile.change_password')}}" class="text-indigo-700 hover:font-semibold">Change Password</a>
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:mr-1 rounded flex flex-row flex-wrap justify-between">
                    <a href="/community/bookReviews/?filter=my_reviews" class="text-indigo-700 hover:font-semibold">My Book Reviews</a>
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:ml-1 rounded flex flex-row flex-wrap justify-between">
                    <a href="/community/bookReviews/?filter=saved_reviews" class="text-indigo-700 hover:font-semibold">Saved Book Reviews</a>
                </div>

            </div>
            <div class="w-full bg-white p-2 mt-3 rounded">
                <p class="w-full">Library Statistics</p>
                <p>You have {{$completedBookCount + $inprogressBookCount}} books in your library.</p>
                <p>You have {{$wishlistBookCount}} books in your wishlist</p>
                <p>You have completed {{$completedBookCount}} books so far.</p>
                <p>You have {{$inprogressBookCount}} books in progress currently.</p>
            </div>
            <div class="w-full bg-white p-2 mt-5 rounded">
                <p class="text-center">Achievements</p>
            </div>
            <div class="w-full flex flex-row justify-around flex-wrap">
                @php
                    $b1 = 'locked';
                    $b2 = 'locked';
                    $b3 = 'locked';
                    $b4 = 'locked';
                    $b5 = 'locked'; 
                    $b6 = 'locked';
                    $b7 = 'locked';
                    $b8 = 'locked';
                    if($completedBookCount >= 1) $b1 = 'unlocked';
                    if($completedBookCount >= 5) $b2 = 'unlocked';
                    if($completedBookCount >= 10) $b3 = 'unlocked';
                    if($completedBookCount >= 20) $b4 = 'unlocked';
                    if($completedBookCount >= 50) $b5 = 'unlocked';
                    if($completedBookCount >= 100) $b6 = 'unlocked';
                    if($completedBookCount >= 200) $b7 = 'unlocked';
                    if($completedBookCount >= 500) $b8 = 'unlocked';
                @endphp
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b1}}">
                    <img src="/resources/trophy1.png" class="w-24 h-auto unlocked" />
                    <p>Beginner</p>
                    @if($b1 == 'locked')
                        <p class="text-xs text-center">Complete 1 book to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b2}}">
                    <img src="/resources/trophy2.png" class="w-20 h-auto" />
                    <p>Level I</p>
                    @if($b2 == 'locked')
                        <p class="text-xs text-center">Read 5 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b3}}">
                    <img src="/resources/trophy3.png" class="w-20 h-auto" />
                    <p>Level II</p>
                    @if($b3 == 'locked')
                        <p class="text-xs text-center">Read 10 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b4}}">
                    <img src="/resources/trophy4.png" class="w-20 h-auto" />
                    <p>Level III</p>
                    @if($b4 == 'locked')
                        <p class="text-xs text-center">Read 20 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b5}}">
                    <img src="/resources/trophy5.png" class="w-20 h-auto" />
                    <p>Professional</p>
                    @if($b5 == 'locked')
                        <p class="text-xs text-center">Read 50 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b6}}">
                    <img src="/resources/trophy6.png" class="w-20 h-auto" />
                    <p>Expert</p>
                    @if($b6 == 'locked')
                        <p class="text-xs text-center">Read 100 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b7}}">
                    <img src="/resources/trophy7.png" class="w-20 h-auto" />
                    <p>Champion</p>
                    @if($b7 == 'locked')
                        <p class="text-xs text-center">Read 200 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b8}}">
                    <img src="/resources/trophy8.png" class="w-20 h-auto" />
                    <p>Legendary</p>
                    @if($b8 == 'locked')
                        <p class="text-xs text-center">Read 500 books to unclock</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="update_name_box" >
        @if($errors->any() && $errors->first() == 'The name field is required.')
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-red-700">{{$error}}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{route('profile.update_name')}}" method="post">
            @method('PUT')
            @csrf 
            <label>New name</label>
            <input type="text" name="name" placeholder="Name" 
                   class="w-full border border-gray-700 p-2 rounded mt-1 mb-3">
            <div class="w-full flex flex-row justify-between" required>
                <button type="submit" class="bg-blue-700 rounded py-1 px-2 text-white hover:bg-blue-800">
                    Update name
                </button>
                <button onclick="invokeUpdateNameBox()" type="button"
                        class="border border-blue-700 rounded py-1 px-2 text-blue-700 hover:font-semibold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    <div id="update_email_box">
        @if($errors->any() && $errors->first() != 'The name field is required.' 
            && $errors->first() != 'The nickname field is required.'
            && $errors->first() != 'The nickname has already been taken.' )
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-red-700">{{$error}}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{route('profile.update_email')}}" method="post">
            @csrf 
            @method('PUT')
            <label>Enter new email</label>
            <input type="email" name="email" placeholder="example@example.com" 
                   class="w-full border border-gray-700 p-2 rounded mb-3 mt-1">
            <div class="w-full flex flex-row justify-between" required>
                <button type="submit" class="bg-blue-700 rounded py-1 px-2 text-white hover:bg-blue-800">
                    Update email
                </button>
                <button onclick="invokeUpdateEmailBox()" type="button"
                        class="border border-blue-700 rounded py-1 px-2 text-blue-700 hover:font-semibold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    <div id="update_nickname_box" >
        @if($errors->any() && ($errors->first() == 'The nickname field is required.'
            || $errors->first() == 'The nickname has already been taken.'))
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-red-700">{{$error}}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{route('profile.update_nickname')}}" method="post">
            @method('PUT')
            @csrf 
            <label>New nickname</label>
            <input type="text" name="nickname" placeholder="Nick name" 
                   class="w-full border border-gray-700 p-2 rounded mt-2 mb-3">
            <input type="checkbox" name="use_nickname" value="1"
                   class="border border-gray-700 p-2 rounded mt-2 mb-3"
                   @if($user->use_nickname == 1) checked @endif>
            <label for="use_nickname">Use nickname in public posts</label>
            <div class="w-full flex flex-row justify-between mt-2">
                <button type="submit" class="bg-blue-700 rounded py-1 px-2 text-white hover:bg-blue-800">
                    Update nickname
                </button>
                <button onclick="invokeUpdateNicknameBox()" type="button"
                        class="border border-blue-700 rounded py-1 px-2 text-blue-700 hover:font-semibold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<style>
    .locked { 
        pointer-events: none;
        opacity: 0.4;
    }
    #update_name_box, 
    #update_email_box, 
    #update_nickname_box {
        visibility: hidden;
        width: 50%;
        position: fixed;
        top: 200px;
        left: 25%;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    @media(max-width: 680px) {
        #update_name_box, 
        #update_email_box, 
        #update_nickname_box {
            width: 96%;
            left:2%;
        }
    }
</style>

<script>
function invokeUpdateNameBox()
{
    if(update_name_box.style.visibility == "visible")
    {
        update_name_box.style.visibility = "hidden";
    }
    else
    {
        update_name_box.style.visibility = "visible";
        if(update_email_box.style.visibility == "visible")
        {
            update_email_box.style.visibility = "hidden";
        }
        if(update_nickname_box.style.visibility == "visible")
        {
            update_nickname_box.style.visibility = "hidden";
        }
    }
}

function invokeUpdateEmailBox()
{
    if(update_email_box.style.visibility == "visible")
    {
        update_email_box.style.visibility = "hidden";
    }
    else
    {
        update_email_box.style.visibility = "visible";
        if(update_nickname_box.style.visibility == "visible")
        {
            update_nickname_box.style.visibility = "hidden";
        }
        if(update_name_box.style.visibility == "visible")
        {
            update_name_box.style.visibility = "hidden";
        }
    }
}

function invokeUpdateNicknameBox()
{
    if(update_nickname_box.style.visibility == "visible")
    {
        update_nickname_box.style.visibility = "hidden";
    }
    else
    {
        update_nickname_box.style.visibility = "visible";
        if(update_email_box.style.visibility == "visible")
        {
            update_email_box.style.visibility = "hidden";
        }
        if(update_name_box.style.visibility == "visible")
        {
            update_name_box.style.visibility = "hidden";
        }
    }
}
</script>