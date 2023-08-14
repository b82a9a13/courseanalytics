$(`#add_c_form`)[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const errorText = $(`#add_c_error`)[0];
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
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/add.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
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
});