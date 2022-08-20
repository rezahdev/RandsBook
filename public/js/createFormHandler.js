function createAuthorInputField(authorNum)
{
    console.log(authorNum);
    
    let field = document.getElementById("author1");
    let clone = field.cloneNode(true);
    clone.id = 'author' + authorNum;
    clone.children[0].name = 'author' + authorNum;
    clone.children[0].value = "";
    clone.children[0].removeAttribute('onkeypress');
    clone.children[0].id = "blah2";
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
        clone.children[0].addEventListener('input', function() { inputAuthorFieldChanged(clone.children[0]); });
        p.addEventListener('click', function(){ createAuthorInputField(`${(authorNum)}`); });
        p.style.display = "none";
    }
    else
    {
        p.textContent = "Cannot add anymore author.";
        p.style.color = "black";
        p.style.curson = "default";
    }

    console.log(author_list.childElementCount);
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
        clone.children[0].addEventListener('input', function() { inputPublisherFieldChanged(clone.children[0]); });
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
    if(author_list.childElementCount < 2)
    {
        alert('Sorry, can\'t delete all author fields. Please leave the author field empty if you do not know the author information. ');
        return;
    }
    console.log(authorId);
    let child = document.getElementById(authorId);
    author_list.removeChild(child);
    
}

function deletePublisher(publisherId)
{
    if(publisher_list.childElementCount < 2)
    {
        alert('Sorry, can\'t delete all publisher fields. Please leave the publisher field empty if you do not know the publisher information. ');
        return;
    }

    console.log(publisher_list.children);
    let child = document.getElementById(publisherId);
    publisher_list.removeChild(child);
    
}

function inputPublisherFieldChanged(input)
{
    console.log("here" + input.id);
    if(input.value.length > 0){
        create_publisher_label.style.display = "block"; 
    }
    else
    {
        create_publisher_label.style.display = "none"; 
    }
}


function inputAuthorFieldChanged(input)
{
    console.log("here" + input.id);
    if(input.value.length > 0){
        create_author_label.style.display = "block"; 
    }
    else
    {
        create_author_label.style.display = "none"; 
    }
}

function addSubjectTag()
{
   
    console.log("here");
    let subjectNumStr = (subject_list.children[subject_list.childElementCount-1].id).replace('subject', '');
    let subjectNum = parseInt(subjectNumStr) + 1;
    if(subject_input_field.value.length > 0)
    {
        if(subject_list.childElementCount == 3)
        {
            alert('Sorry, you can\'t add more than three subject tags per each book. In order to add this tag, please delete an existing tag first.');
            return;
        }

        let field = document.getElementById('subject1');
        let clone = field.cloneNode(true);
        subject_list.appendChild(clone);
        clone.id = 'subject' + subjectNum;
        clone.children[0].name='subject' + subjectNum;
        clone.children[0].value = subject_input_field.value;
        clone.children[1].children[0].textContent = subject_input_field.value;

        subject_input_field.value = "";
    }
}