function openDeletePopupBox()
    {
        delete_popup_box.style.visibility = "visible";
        blurrBackground();    
    }

    function closePopupBox(popupBoxId)
    {
        const popupBox = document.getElementById(popupBoxId);
        popupBox.style.visibility = "hidden";

        if(main_content.classList.contains("blurry"))
        {
            main_content.classList.remove("blurry");
        }
    }

    function updateReadPagesInputValue()
    {
        read_pages.value = read_pages_ranger.value;
        update_read_pages_btn.removeAttribute('disabled');
        update_read_pages_btn.classList.remove('bg-gray-700');
        update_read_pages_btn.classList.add('bg-blue-700');
        update_read_pages_btn.classList.add('hover:bg-blue-800');    
    }

    function updateReadPagesRangerValue()
    {
        if(!Number.isInteger(+read_pages.value))
        {
            alert('Pages read must be an integer');
            read_pages.value = read_pages.oldValue;
            return;
        }
        
        if(parseInt(read_pages.value) > parseInt(read_pages_ranger.max))
        {
            alert('Pages read cannot be greater than total pages.');
            read_pages.value = read_pages.oldValue;
            return;
        }

        read_pages_ranger.value = read_pages.value;
        update_read_pages_btn.removeAttribute('disabled');
        update_read_pages_btn.classList.remove('bg-gray-700');
        update_read_pages_btn.classList.add('bg-blue-700');
        update_read_pages_btn.classList.add('hover:bg-blue-800');
    }

    function submitUpdateReadPagesForm()
    {
        submit_read_pages_btn.click();
    }

    function showUpdateReadPageSuccessMsg()
    {
        update_read_pages_success_box.style.visibility = "visible";
        blurrBackground();
    }

    function blurrBackground()
    {
        if(!main_content.classList.contains("blurry"))
        {
            main_content.classList.add("blurry");
        }
    }