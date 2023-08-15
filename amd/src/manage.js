//Code below is used to display the list of tracked courses and to change the text of "Show list"
const sorh = $(`#showorhide`)[0];
sorh.addEventListener('click', ()=>{
    const list = $('#courselist')[0];
    list.style.display = (list.style.display === 'none') ? 'block' : 'none';
    sorh.innerText = (sorh.innerText.includes('Show')) ? sorh.innerText.replace('Show','Hide') : sorh.innerText.replace('Hide','Show');
});
function render(type, opt){
    const errorText = $(`#${opt}_error`)[0];
    errorText.style.display = 'none';
    const btn = $(`#${type}_btn`)[0];
    const div = $(`#${opt}_content`)[0];
    div.innerHTML = '';
    div.style.display = 'none';
    let array = [];
    if(opt === 'table'){
        array = ['cd', 'al', 'nau', 'eh', 'nuh'];
    } else if(opt === 'chart'){
        array = ['eu'];
    }
    if(array != []){
        if(btn.innerText.includes('Show')){
            array.forEach((item)=>{
                $(`#${item}_btn`)[0].innerText = $(`#${item}_btn`)[0].innerText.replace('Hide', 'Show');
            })
            btn.innerText = btn.innerText.replace('Show', 'Hide');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `./classes/inc/${opt}_render.inc.php`, true);
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
                    errorText.innerText = 'Connection error.';
                    errorText.style.display = 'block';
                }
            }
            xhr.send(`type=${type}`);
        } else if(btn.innerText.includes('Hide')){
            btn.innerText = btn.innerText.replace('Hide', 'Show');
        }
    }
}
function headerClicked(string, integer){
    const headers = $(`#${string}_thead`).find('tr:first th');
    for(let i = 0; i < headers.length; i++){
        if(i === integer){
            if(headers[i].getAttribute('sort') === 'asc'){
                headers[i].setAttribute('sort', 'desc');
                headers[i].innerHTML = headers[i].innerHTML.replace('↑', '↓');
            } else if(headers[i].getAttribute('sort') === 'desc'){
                headers[i].setAttribute('sort', 'asc');
                headers[i].innerHTML = headers[i].innerHTML.replace('↓', '↑');
            } else if(headers[i].getAttribute('sort') === ''){
                headers[i].innerHTML = headers[i].innerHTML+" ↑";
                headers[i].setAttribute('sort', 'asc');
            }
        } else {
            headers[i].setAttribute('sort', '');
            headers[i].innerHTML = headers[i].innerHTML.replace('↑', '');
            headers[i].innerHTML = headers[i].innerHTML.replace('↓', '');
        }
    }
    const body = $(`#${string}_tbody`).find('tr');
}