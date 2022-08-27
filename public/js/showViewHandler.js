function openDeletePopupBox() 
{
    delete_popup_box.style.visibility = "visible";
    blurrBackground();
}

function closePopupBox(popupBoxId) 
{
    const popupBox = document.getElementById(popupBoxId);
    popupBox.style.visibility = "hidden";

    if (main_content.classList.contains("blurry")) 
    {
        main_content.classList.remove("blurry");
    }
}

function updateReadPagesInputValue() 
{
    read_pages.value = read_pages_ranger.value;
    update_read_pages_btn.classList.remove('hidden');
}

function updateReadPagesRangerValue() 
{
    if (!Number.isInteger(+read_pages.value)) 
    {
        alert('Pages read must be an integer');
        read_pages.value = read_pages.oldValue;
        return;
    }

    if (parseInt(read_pages.value) > parseInt(read_pages_ranger.max)) 
    {
        alert('Pages read cannot be greater than total pages.');
        read_pages.value = read_pages.oldValue;
        return;
    }

    read_pages_ranger.value = read_pages.value;
    update_read_pages_btn.classList.remove('hidden');
}

function showUpdateReadPageSuccessMsg() 
{
    update_read_pages_success_box.style.visibility = "visible";
    blurrBackground();
}

function blurrBackground() 
{
    if (!main_content.classList.contains("blurry")) 
    {
        main_content.classList.add("blurry");
    }
}

function updateReadPages(bookId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = '/updateReadPages';
    let formData = new FormData();

    formData.append('book_id', bookId);
    formData.append('read_pages', read_pages.value);
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
                showUpdateReadPageSuccessMsg();
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function addToLibrary(bookId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = '/wishlistToLibrary';
    let formData = new FormData();

    formData.append('book_id', bookId);
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
                alert(responseObj.message);
                window.location.replace('/book/'+bookId);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function removeFromWishlist(bookId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = '/wishlist/remove';
    let formData = new FormData();

    formData.append('book_id', bookId);
    formData.append('_token', csrfToken);
    formData.append('_method', 'DELETE');

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                alert(responseObj.message);
                window.location.replace('/wishlist');
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}