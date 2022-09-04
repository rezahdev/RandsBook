window.onscroll = function () 
{
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) 
    {
        scroll_to_top.classList.remove("hidden");
    } 
    else 
    {
        scroll_to_top.classList.add("hidden");
    }
}
function scrollToTop() 
{
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function invokeSortOptionsBox()
{
    if(sort_options_box.style.visibility == "visible")
    {
        sort_options_box.style.visibility = "hidden";
    }
    else
    {  
        const right = Math.round((innerWidth - container.offsetWidth)/2);
        sort_options_box.style.right = right + 'px';
        sort_options_box.style.visibility = "visible";
    }
}

function openDeletePopupBox(reviewId) 
{
    delete_popup_box.style.visibility = "visible";
    review_id_to_delete.textContent = reviewId;
    
    if (!container.classList.contains("blurry")) 
    {
        container.classList.add("blurry");
    }
}

function closeDeletePopupBox() 
{
    delete_popup_box.style.visibility = "hidden";

    if (container.classList.contains("blurry")) 
    {
        container.classList.remove("blurry");
    }
    
}

function showFullReview(showBtn, hideBtnId, reviewTextId, review)
{
    const reviewText = document.getElementById(reviewTextId);
    reviewText.textContent = review.replace(/['"]+/g, '');
    showBtn.style.display = "none";
    document.getElementById(hideBtnId).style.display = "block";
}

function hideFullReview(hideBtn, showBtnId, reviewTextId, review)
{
    const reviewText = document.getElementById(reviewTextId);
    reviewText.textContent = review.replace(/['"]+/g, '');
    hideBtn.style.display = "none";
    document.getElementById(showBtnId).style.display = "block";
}

function likeReview(likeBtn, reviewId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = "/community/bookReviews/like";
    let formData = new FormData();

    formData.append('review_id', reviewId);
    formData.append('_token', csrfToken);

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                const btn = likeBtn.cloneNode(true);
                btn.removeAttribute('onclick');
                btn.children[0].src = '/resources/like_filled.png';
                btn.children[1].textContent = parseInt(btn.children[1].textContent) + 1;
                btn.addEventListener('click', function() { unlikeReview(btn, reviewId, csrfToken) });
                likeBtn.parentNode.replaceChild(btn, likeBtn);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function unlikeReview(likeBtn, reviewId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = "/community/bookReviews/unlike";
    let formData = new FormData();

    formData.append('review_id', reviewId);
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
                const btn = likeBtn.cloneNode(true);
                btn.removeAttribute('onclick');
                btn.children[0].src = '/resources/like_blank.png';
                btn.children[1].textContent = parseInt(btn.children[1].textContent) - 1;
                btn.addEventListener('click', function() { likeReview(btn, reviewId, csrfToken) });
                likeBtn.parentNode.replaceChild(btn, likeBtn);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function saveReview(saveBtn, reviewId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = "/community/bookReviews/save";
    let formData = new FormData();

    formData.append('review_id', reviewId);
    formData.append('_token', csrfToken);

    http.open('POST', url, true);

    http.onreadystatechange = function() 
    {
        if(http.readyState == 4 && http.status == 200) 
        {
            let responseObj = JSON.parse(http.responseText);
            if(responseObj.response == 'OK')
            {
                const btn = saveBtn.cloneNode(true);
                btn.removeAttribute('onclick');
                btn.children[0].src = '/resources/save_filled.png';
                btn.addEventListener('click', function() { unsaveReview(btn, reviewId, csrfToken) });
                saveBtn.parentNode.replaceChild(btn, saveBtn);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function unsaveReview(saveBtn, reviewId, csrfToken)
{
    let http = new XMLHttpRequest();
    let url = "/community/bookReviews/unsave";
    let formData = new FormData();

    formData.append('review_id', reviewId);
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
                const btn = saveBtn.cloneNode(true);
                btn.removeAttribute('onclick');
                btn.children[0].src = '/resources/save_blank.png';
                btn.addEventListener('click', function() { saveReview(btn, reviewId) });
                saveBtn.parentNode.replaceChild(btn, saveBtn, csrfToken);
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}

function deleteReview(csrfToken)
{
    let reviewId = parseInt(review_id_to_delete.textContent);
    let http = new XMLHttpRequest();
    let url = "/community/bookReviews/delete";
    let formData = new FormData();

    formData.append('review_id', reviewId);
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
                const review = document.getElementById('review' + reviewId);
                review.parentNode.removeChild(review);
                review_id_to_delete.textContent = null;
                closeDeletePopupBox();
            }
            else
            {
                alert(responseObj.message);
            }
        }
    }
    http.send(formData);
}