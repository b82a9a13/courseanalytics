//Listens for a form submit for a specific id
$(`#remove_c_form`)[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    //Define the error element and create the paramaters for the request
    const errorText = $(`#remove_c_error`)[0];
    errorText.style.display = 'none';
    let params = '';
    if($(`#course_all`)[0].checked){
        params += 'type=all';
    } else {
        params += 'type=select';
        let total = 0;
        $('.course-options').each(function(index, element){
            if($(element)[0].checked){
                params += `&c${total}=${$(element)[0].value}`;
                total++;
            }
        });
        params += `&total=${total}`;
    }
    //create the request and send it
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/remove.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            //Handle the response
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorText.innerText = text['error'];
                errorText.style.display = 'block';
            } else if(text['return']){
                window.location.href = './manage.php';
            } else {
                errorText.innerText = 'Submit error';
                errorText.style.display = 'block';
            }
        }
    }
    xhr.send(params);
})