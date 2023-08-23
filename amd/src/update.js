//Used to change the attribute "changed" for later use in identifying if a company value has changed
function changed_company(element){
    if(element.value === ''){
        element.setAttribute('changed', 'false');
    } else {
        element.setAttribute('changed', 'true');
    }
}
function update_companys(){
    const errorText = $(`#update_error`)[0];
    const successText = $(`#update_success`)[0];
    successText.style.display = 'none';
    errorText.style.display = 'none';
    const inputs = $(`.update-company`);
    let params = '';
    let int = 0;
    inputs.each(function(index, element){
        element.style.border = '';
        if(element.getAttribute('changed') == 'true'){
            params += (int === 0) ? `c${int}=${element.value}&i${int}=${element.getAttribute('uid')}` : `&c${int}=${element.value}&i${int}=${element.getAttribute('uid')}`;
            int++;
        }
    });
    params += (int > 0) ? `&total=${int}` : '';
    if(params === ''){
        errorText.innerText = 'No changes made.';
        errorText.style.display = 'block';
    } else {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/update.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    if(Array.isArray(text['error'])){
                        $('input.update-company[uid="'+text['error'][0]+'"]')[0].style.border = '2px solid red';
                        errorText.innerText = text['error'][1];
                        errorText.style.display = 'block';
                    } else {
                        errorText.innerText = text['error'];
                        errorText.style.display = 'block';
                    }
                } else if(text['return']){
                    successText.innerText = 'Updated';
                    successText.style.display = 'block';
                    inputs.each(function(index, element){
                        element.setAttribute('changed', 'false');
                    });
                } else {
                    errorText.innerText = 'Update error';
                    errorText.style.display = 'block';
                }
            } else {
                errorText.innerText = 'Connection error.';
                errorText.style.display = 'block';
            }
        }
        xhr.send(params);
    }
}