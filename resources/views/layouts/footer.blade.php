<div id="footer" class="w-full bg-white p-5 text-sm text-gray-700 flex flex-col items-center">
    <div class="flex flex-row flex-center">
        <p onclick="showAbout()" class="mx-2 hover:text-blue-700 cursor-pointer">About</p>
        <p onclick="showAbout()" class="mx-2 hover:text-blue-700 cursor-pointer">License</p>
        <a href="/credits" class="mx-2 hover:text-blue-700">Credits</a>
    </div>
    <div>
        <p>Copyright&copy; Randsbook.com @php date('Y') @endphp</p>
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
    <button onclick="closeAbout()" class="bg-blue-700 py-2 w-24 text-white rounded hover:bg-blue-800 mt-5">Ok</button>
</div>

<script>
window.onload = function () 
{
    let docHeight = document.getElementById('main').clientHeight;
    
    if (innerHeight > docHeight) 
    {
        footer.classList.add("fixed");
        footer.classList.add("bottom-0");
    } 
}

function showAbout()
{
    if(about.classList.contains('hidden'))
    {
        about.classList.remove('hidden');
    }
}

function closeAbout()
{
    if(!about.classList.contains('hidden'))
    {
        about.classList.add('hidden');
    }
}
</script>