/**
 * Function to create an additional input field for author name
 * @param { int } authorNum (Number of current author fields in the form + 1)
 */
function createAuthorInputField(authorNum)
{ 
    if(authorNum > 15)
    {
        alert('Sorry, you have tried to create author field too many times. You can refresh the page and restart to add new authors.');
        return;
    }
    const authorField = document.getElementById("author1");
    const authorFieldClone = authorField.cloneNode(true);
    authorFieldClone.id = 'author' + authorNum;

    //Input
    authorFieldClone.children[0].name = 'author' + authorNum;
    authorFieldClone.children[0].value = "";
    authorFieldClone.children[0].removeAttribute('onkeypress');

    //close button
    authorFieldClone.children[1].removeAttribute('onclick'); 
    authorFieldClone.children[1].addEventListener('click', function() { deleteAuthorField(`${authorFieldClone.id}`); });
    
    author_list.appendChild(authorFieldClone);

    //Label that is used to create to another author input field
    const createAuthorLabelClone = create_author_label.cloneNode(true);
    create_author_label.parentNode.replaceChild(createAuthorLabelClone, create_author_label);
    createAuthorLabelClone.id = "create_author_label";
    
    if(createAuthorLabelClone.hasAttributes('onclick'))
    {
        createAuthorLabelClone.removeAttribute('onclick');
    }
    

    if(author_list.childElementCount <= 10)
    {
        authorFieldClone.children[0].addEventListener('input', function() { inputAuthorFieldChanged(authorFieldClone.children[0]) });
        createAuthorLabelClone.addEventListener('click', function(){ createAuthorInputField(`${(++authorNum)}`); });
        createAuthorLabelClone.setAttribute('hasListener', 'true');
        createAuthorLabelClone.style.display = "none";
    }
    else
    {
        createAuthorLabelClone.textContent = "Cannot add anymore author.";
        createAuthorLabelClone.style.color = "black";
        createAuthorLabelClone.style.curson = "default";
        createAuthorLabelClone.setAttribute('hasListener', 'false');
    }
}

/**
 * Function to create input field for publisher name
 * @param { int } publisherNum (Number of current publisher fields in the form + 1)
 */
function createPublisherInputField(publisherNum)
{
    if(publisherNum > 15)
    {
        alert('Sorry, you have tried to create publisher field too many times. You can refresh the page and restart to add new publishers.');
        return;
    }

    const publisherField = document.getElementById("publisher1");
    const publisherFieldClone = publisherField.cloneNode(true);
    publisherFieldClone.id = 'publisher' + publisherNum;

    //Input
    publisherFieldClone.children[0].name = 'publisher' + publisherNum;
    publisherFieldClone.children[0].value = "";
    publisherFieldClone.children[0].addEventListener('input', function() { create_publisher_label.style.display = "block"; });

    //close button
    publisherFieldClone.children[1].removeAttribute('onclick');
    publisherFieldClone.children[1].addEventListener('click', function() { deletePublisherField(`${publisherFieldClone.id}`); });
    
    publisher_list.appendChild(publisherFieldClone);

    const createPublisherLabelClone = create_publisher_label.cloneNode(true);
    create_publisher_label.parentNode.replaceChild(createPublisherLabelClone, create_publisher_label);
    createPublisherLabelClone.id = "create_publisher_label";

    if(createPublisherLabelClone.hasAttributes('onclick'))
    {
        createPublisherLabelClone.removeAttribute('onclick');
    }

    if(publisher_list.childElementCount <= 4)
    {
        publisherFieldClone.children[0].addEventListener('input', function() 
        { 
            inputPublisherFieldChanged(publisherFieldClone.children[0]); 
        });
        createPublisherLabelClone.addEventListener('click', function(){ createPublisherInputField(`${(++publisherNum)}`); });
        createPublisherLabelClone.style.display = "none";
        createPublisherLabelClone.setAttribute('hasListener', 'true');
    }
    else
    {
        createPublisherLabelClone.textContent = "Cannot add anymore publisher.";
        createPublisherLabelClone.style.color = "black";
        createPublisherLabelClone.style.curson = "default";
        createPublisherLabelClone.setAttribute('hasListener', 'false');
    }
}

/**
 * Function to add a new subject tag
 * @param { int } subjectNum (Number of current subject tags in the form + 1) 
 */
 function addSubjectTag(subjectNum)
 {
    if(subjectNum > 15)
    {
        alert('Sorry, you have tried to create subject field too many times. You can refresh the page and restart to add new subjects.');
        return;
    }

    if(subject_input_field.value.length > 0)
    {
        if(subject_list.childElementCount == 4)
        {
            alert('Sorry, you can\'t add more than three subject tags per each book. In order to add this tag, please delete an existing tag first.');
            return;
        }

        const subjectField = document.getElementById('subject0');
        const subjectFieldClone = subjectField.cloneNode(true);
        subjectFieldClone.id = 'subject' + subjectNum;
        subjectFieldClone.style.display = "block";

        //subject badge
        subjectFieldClone.children[0].name='subject' + subjectNum;
        subjectFieldClone.children[0].value = subject_input_field.value;
        subjectFieldClone.children[1].children[0].textContent = subject_input_field.value;

        //delete button
        subjectFieldClone.children[1].children[1].removeAttribute("onclick");
        subjectFieldClone.children[1].children[1].addEventListener('click', function() { deleteSubjectTag(`subject${subjectNum}`); });

        subject_list.appendChild(subjectFieldClone);
        subject_input_field.value = "";

        //update the add_subject_tag_label
        const addSubjectTagLabelClone = add_subject_tag_label.clone(true);
        add_subject_tag_label.parentNode.replaceChild(addSubjectTagLabelClone, add_subject_tag_label);
        addSubjectTagLabelClone.id = "add_subject_tag_label";

        if(addSubjectTagLabelClone.hasAttributes('onclick'))
        {
            addSubjectTagLabelClone.removeAttribute('onclick');
        }
        addSubjectTagLabelClone.addEventListener('click', function() { addSubjectTag(`${++subjectNum}`) });
    }
 }

/**
 * Function to delete an author input field
 * @param { string } authorId  Id of the author Input field to be deleted
 */
function deleteAuthorField(authorId)
{
    if(author_list.childElementCount < 2)
    {
        alert('Sorry, can\'t delete all author fields. Please leave the author field empty if you do not know the author information. ');
        return;
    }

    const authorField = document.getElementById(authorId);
    author_list.removeChild(authorField);

    if(author_list.childElementCount <= 10 && create_author_label.getAttribute('hasListener') !== 'true')
    {
        let authorNumStr = (author_list.children[author_list.childElementCount-1].id).replace('author', '');
        let authorNum = parseInt(authorNumStr) + 1;

        create_author_label.addEventListener('click', function(){ createAuthorInputField(`${(authorNum)}`); });
        create_author_label.style.display = "block";
        create_author_label.style.color = "blue";
        create_author_label.textContent = "Add another author";
        create_author_label.setAttribute('hasListener', 'true');
    }

    if(author_list.childElementCount == 1 && create_author_label.style.display == "none")
    {
        create_author_label.style.display = "block";
    }
}

/**
 * Function to delete a publisher input field
 * @param { string } publisherId Id of the publisher input field to be deleted
 */
function deletePublisherField(publisherId)
{
    if(publisher_list.childElementCount < 2)
    {
        alert('Sorry, can\'t delete all publisher fields. Please leave the publisher field empty if you do not know the publisher information. ');
        return;
    }

    const publisherField = document.getElementById(publisherId);
    publisher_list.removeChild(publisherField);   

    if(publisher_list.childElementCount <= 10 && create_publisher_label.getAttribute('hasListener') !== 'true')
    {
        let publisherNumStr = (publisher_list.children[publisher_list.childElementCount-1].id).replace('publisher', '');
        let publisherNum = parseInt(publisherNumStr) + 1;

        create_publisher_label.addEventListener('click', function(){ createPublisherInputField(`${(publisherNum)}`); });
        create_publisher_label.style.display = "block";
        create_publisher_label.style.color = "blue";
        create_publisher_label.textContent = "Add another publisher";
        create_publisher_label.setAttribute('hasListener', 'true');
    }

    if(publisher_list.childElementCount == 1 && create_publisher_label.style.display == "none")
    {
        create_publisher_label.style.display = "block";
    }
}

/**
 * Function to delete a subject tag
 * @param { string } subjectId id of the subject field to be deleted
 * @returns 
 */
 function deleteSubjectTag(subjectId)
 {
     if(subjectId == "subject0") return;
 
     const subjectTag = document.getElementById(subjectId);
     subject_list.removeChild(subjectTag);    
 }

/**
 * Function to display or hide create_author_label based on author field input value change
 * @param { input } input 
 */
function inputAuthorFieldChanged(input)
{
    if(input.value.length > 0)
    {
        create_author_label.style.display = "block"; 
    }
    else
    {
        create_author_label.style.display = "none"; 
    }
}

/**
 * Function to display or hide create_publisher_label based on publisher field input value change
 * @param { input } input 
 */
function inputPublisherFieldChanged(input)
{
    if(input.value.length > 0)
    {
        create_publisher_label.style.display = "block"; 
    }
    else
    {
        create_publisher_label.style.display = "none"; 
    }
}