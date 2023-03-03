<div id="footer" class="w-full bg-white p-5 text-sm text-gray-700 flex flex-col items-center">
    <div class="flex flex-row flex-center">
        <p onclick="showAbout()" class="mx-2 hover:text-blue-700 cursor-pointer">About</p>
        <p onclick="showAbout()" class="mx-2 hover:text-blue-700 cursor-pointer">License</p>
        <p onclick="showCredits()" class="mx-2 hover:text-blue-700 cursor-pointer">Credits</p>
    </div>
    <div>
        <p>Copyright &copy;Randsbook.com @php date('Y') @endphp</p>
    </div>
</div>
<div id="about" class="hidden w-[calc(100%-2rem)] ml-4 fixed bg-white rounded p-5 flex flex-col items-center" style="top:100px">
    <h2 class="font-bold mb-2">About Randsbook.com</h2>
    <p>
        Randsbook.com is a demo version of a library app that can help users search, add, and manage books through one single app. 
        It is an open-source app provided under the MIT License. You are welcome to create an account
        for the purpose of exploring the app. 
    </p>
    <p class="text-sm float-right mt-2">- Reza saker (Developer)</p>
    <button onclick="closeAbout()" class="bg-blue-700 py-2 w-24 text-white rounded hover:bg-blue-800 mt-5">Close</button>
</div>
<div id="credits" class="hidden w-[calc(100%-2rem)] ml-4 absolute bg-white rounded p-5 mb-48 flex flex-col items-center" style="top:100px;">
    <h2 class="font-bold mb-2">Acknowledgements</h2>
    <a class="a" href="https://openlibrary.org">Book search functionality is powered by Open Library API</a>
    <a class="a" href="https://www.flaticon.com/free-icons/heart" title="heart icons">Heart icons created by Kiranshastry - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/filter" title="filter icons">Filter icons created by herikus - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/up-and-down-arrow" title="up and down arrow icons">Up and down arrow icons created by syafii5758 - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/edit" title="edit icons">Edit icons created by Pixel perfect - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/prize" title="prize icons">Prize icons created by Freepik - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/trophy" title="trophy icons">Trophy icons created by Freepik - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/medal" title="medal icons">Medal icons created by Freepik - Flaticon</a>
    <a class="a" href="https://github.com/yiliansource/party-js">Confetti effects is provided by Party.js Library on Github</a>
    <a class="a" href="https://www.freepik.com/free-vector/white-bookshelf-mockup-books-shelf-library_8308785.htm#query=library&position=37&from_view=search">Image by upklyak on Freepik</a>
    <a class="a" href="https://www.flaticon.com/free-icons/save" title="save icons">Save icons created by Yogi Aprelliyanto - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/arrow" title="arrow icons">Arrow icons created by 88 Cloud - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/delete" title="delete icons">Delete icons created by Kiranshastry - Flaticon</a>
    <a class="a" href="https://www.flaticon.com/free-icons/modify" title="modify icons">Modify icons created by Freepik - Flaticon</a>
    <a class="a" href="https://www.pexels.com/photo/light-inside-library-590493/" title="background image">Photo by Janko Ferlic on Pexels </a>
    <button onclick="closeCredits()" class="bg-blue-700 py-2 w-24 text-white rounded hover:bg-blue-800 mt-5">Close</button>
</div>

<script>
window.onload = function () {
    let docHeight = document.getElementById('main').clientHeight + 200;
    
    if (innerHeight > docHeight) {
        footer.classList.add("fixed");
        footer.classList.add("bottom-0");
    } 
}

function showAbout() {
    if(about.classList.contains('hidden')) {
        about.classList.remove('hidden');
    }
}

function closeAbout() {
    if(!about.classList.contains('hidden')) {
        about.classList.add('hidden');
    }
}

function showCredits() {
    if(credits.classList.contains('hidden')) {
        credits.classList.remove('hidden');
    }
}

function closeCredits() {
    if(!credits.classList.contains('hidden')) {
        credits.classList.add('hidden');
    }
}
</script>

<style>
a.a {
    color: blue;
    margin-top: 5px;
    display: block;
    padding: 5px;
    float: left;
}

#footer {
    box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);
}
</style>
