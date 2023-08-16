$(`#search_form`)[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const errorText = $(`#search_error`)[0];
    errorText.style.display = 'none';
    const div = $(`#result_div`)[0];
    div.style.display = 'none';
    const username = $(`#username`)[0];
    const lastname = $(`#lastname`)[0];
    const firstname = $(`#firstname`)[0];
    const email = $(`#email`)[0];
    const city = $(`#city`)[0];
    const company = $(`#company`)[0];
    if(username.value === '' && lastname.value === ''  && firstname.value === '' && email.value === '' && city.value === '' && company.value === ''){
        errorText.innerText = 'No input provided';
        errorText.style.display = 'block';
    } else {
        const params = `username=${username.value}&lastname=${lastname.value}&firstname=${firstname.value}&email=${email.value}&city=${city.value}&company=${company.value}`;
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/search.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['return']){
                    div.innerHTML = text['return'];
                    div.style.display = 'block';
                } else {
                    errorText.innerText = 'Loading error';
                    errorText.style.display = 'block';
                }
            } else {
                errorText.innerText = 'Connection error';
                errorText.style.display = 'block';
            }
        }
        xhr.send(params);
    }
})