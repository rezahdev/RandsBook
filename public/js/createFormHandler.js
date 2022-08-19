function createAuthorInputField(authorNum)
{
    console.log(authorNum);
    
    let field = document.getElementById("author1");
    let clone = field.cloneNode(true);
    clone.id = 'author' + authorNum;
    clone.children[0].name = 'author' + authorNum;
    clone.children[0].value = "";
    clone.children[1].removeAttribute('onclick');
    clone.children[1].addEventListener('click', function() { deleteAuthor(`${clone.id}`); });
    console.log(clone.id);
    console.log(clone);
    //clone.children[0].value = "";
    //let inputField = document.createElement("input");
    //inputField.name = 'author' + authorNum;
    //inputField.classList.add("authors");
    //inputField.classList.add("rounded");
    //inputField.classList.add("cmr-5");
    author_list.appendChild(clone);

    let p = create_author_label.cloneNode(true);
    create_author_label.parentNode.replaceChild(p, create_author_label);
    p.id = "create_author_label";
    p.removeAttribute('onclick');
    
    authorNum++;
    if(authorNum <= 10)
    {
        clone.children[0].addEventListener('input', function() { 
            if(clone.children[0].value.length > 0){
                create_author_label.style.display = "block"; 
            }
            else
            {
                create_author_label.style.display = "none"; 
            }
        });
        p.addEventListener('click', function(){ createAuthorInputField(`${(authorNum)}`); });
        p.style.display = "none";
    }
    else
    {
        p.textContent = "Cannot add anymore author.";
        p.style.color = "black";
        p.style.curson = "default";
    }
}

function createPublisherInputField(publisherNum)
{
    let field = document.getElementById("publisher1");
    let clone = field.cloneNode(true);
    clone.id = 'publisher' + publisherNum;
    clone.children[0].name = 'publisher' + publisherNum;
    clone.children[0].value = "";
    clone.children[0].addEventListener('input', function() { create_publisher_label.style.display = "block"; });
    clone.children[1].removeAttribute('onclick');
    clone.children[1].addEventListener('click', function() { deletePublisher(`${clone.id}`); });
    console.log(clone.id);
    console.log(clone);
    //inputField.classList.add("publishers");
    //inputField.classList.add("rounded");
    //inputField.classList.add("cmr-5");
    publisher_list.appendChild(clone);

    let p = create_publisher_label.cloneNode(true);
    create_publisher_label.parentNode.replaceChild(p, create_publisher_label);
    p.id = "create_publisher_label";
    p.removeAttribute('onclick');

    publisherNum++;
    if(publisherNum <= 4)
    {
        clone.children[0].addEventListener('input', function() { 
            if(clone.children[0].value.length > 0){
                create_publisher_label.style.display = "block"; 
            }
            else
            {
                create_publisher_label.style.display = "none"; 
            }
        });
        p.addEventListener('click', function(){ createPublisherInputField(`${(publisherNum)}`); });
        p.style.display = "none";
    }
    else
    {
        p.textContent = "Cannot add anymore publisher.";
        p.style.color = "black";
        p.style.curson = "default";
    }
}

function deleteAuthor(authorId)
{
    console.log(authorId);
    let child = document.getElementById(authorId);
    author_list.removeChild(child);
    
}

function deletePublisher(publisherId)
{
    console.log(publisherId);
    let child = document.getElementById(publisherId);
    publisher_list.removeChild(child);
    
}