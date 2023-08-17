//Code below is used to display the list of tracked courses and to change the text of "Show list"
const sorh = $(`#showorhide`)[0];
sorh.addEventListener('click', ()=>{
    const list = $('#courselist')[0];
    list.style.display = (list.style.display === 'none') ? 'block' : 'none';
    sorh.innerText = (sorh.innerText.includes('Show')) ? sorh.innerText.replace('Show','Hide') : sorh.innerText.replace('Hide','Show');
});
//function is used to retrieve the html used to render tables/charts
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
function history_filter(type){
    const errorText = $(`#hf_error`)[0];
    errorText.style.display = 'none';
    if(type == 'eh' || type == 'nuh'){
        const startdate = $(`#startdate`)[0];
        const enddate = $(`#enddate`)[0];
        if(startdate.value == ''){
            errorText.innerText = 'No start date provided';
            errorText.style.display = 'block';
        } else if(enddate.value == ''){
            errorText.innerText = 'No end date provided';
            errorText.style.display = 'block';
        } else{
            const params = `type=${type}&sd=${startdate.value}&ed=${enddate.value}`;
            const xhr = new XMLHttpRequest()
            xhr.open('POST', './classes/inc/history_render.inc.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function(){
                if(this.status == 200){
                    const text = JSON.parse(this.responseText);
                    if(text['error']){
                        errorText.innerText = text['error'];
                        errorText.style.display = 'block';
                    } else if(text['return']){
                        $(`#${type}_tbody`)[0].innerHTML = text['return'];
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
    }
}