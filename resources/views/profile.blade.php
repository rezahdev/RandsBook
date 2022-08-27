<x-app-layout>
    <div class="flex justify-center w-full flex-wrap">
        <div class="flex justify-between flex-wrap w-11/12 md:w-4/5 mb-10" id="container">
            <div class="w-full flex flex-row flex-wrap">
                <div id="name" class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:mr-1 rounded flex flex-row flex-wrap justify-between">
                    <p class="w-1/3">Name: {{$user->name}}</p>
                    <img src="/resources/pen.png" width="24" height="24" onclick="invokeUpdateNameBox()" class="cursor-pointer hover:scale-105">
                </div>

                <div class="w-full md:w-[calc(50%-4px)] bg-white p-2 mt-3 md:ml-1 rounded flex flex-row flex-wrap justify-between">
                    <p class="w-1/3">Email: {{$user->email}}</p>
                    <img src="/resources/pen.png" width="24" height="24" class="cursor-pointer hover:scale-105">
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
                    <p>Learner</p>
                    @if($b1 == 'locked')
                        <p class="text-xs text-center">Complete first book to unclock</p>
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
                    <p>Expert</p>
                    @if($b5 == 'locked')
                        <p class="text-xs text-center">Read 50 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b6}}">
                    <img src="/resources/trophy6.png" class="w-20 h-auto" />
                    <p>Great</p>
                    @if($b6 == 'locked')
                        <p class="text-xs text-center">Read 100 books to unclock</p>
                    @endif
                </div>
                <div class="w-[calc(50%-0.5rem)] md:w-36 md:h-44 bg-white flex flex-col items-center justify-around mt-2 p-3 rounded-lg {{$b7}}">
                    <img src="/resources/trophy7.png" class="w-20 h-auto" />
                    <p>Excellent</p>
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
    <div id="update_name_box">
        <input name="user_name" id="user_name" placeholder="New name" class="w-full border border-gray-700 p-2 rounded mb-2">
        <button onclick="updateUserName()" class="w-24 bg-blue-700 rounded py-2 text-white hover:bg-blue-800">
            Update
        </button>
        <button onclick="invokeUpdateNameBox()" class="w-24 border border-blue-700 rounded py-2 text-blue-700 hover:font-semibold">
            Cancel
        </button>
    </div>
</x-app-layout>
<style>
    .locked { 
        pointer-events: none;
        opacity: 0.4;
    }
    #update_name_box {
        visibility: hidden;
        width: 50%;
        position: fixed;
        top: 200px;
        left: 25%;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    @media(max-width: 680px)
    {
        #update_name_box
        {
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
    }
}

function updateUserName()
{
    let http = new XMLHttpRequest();
    let url = "{{route('profile.update_name')}}";
    let csrfToken = '{{csrf_token()}}';
    let formData = new FormData();

    formData.append('name', user_name.value);
    formData.append('_token', csrfToken);
    formData.append('_method', 'PUT');

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                name.textContent = user_name.value;
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}
</script>