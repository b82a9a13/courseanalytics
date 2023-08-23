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
        $('#chart_div')[0].style.display = 'none';
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
                        if((opt != 'chart')){
                            div.style.display = 'block';
                        }
                        if(text['script']){
                            let script = document.createElement('script');
                            script.innerHTML = 'drawChart([';
                            text['script'].forEach((item)=>{
                                script.innerHTML += '['+item[0]+',"'+item[1]+'"],';
                            })
                            script.innerHTML = script.innerHTML.slice(0, -1);
                            script.innerHTML += '])';
                            div.appendChild(script);
                        }
                        if(opt === 'chart'){
                            $('#chart_div')[0].style.display = 'block';
                        }
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
//Function is used to retrieve data for a specific start and end date
function history_filter(type){
    const errorText = $(`#hf_error`)[0];
    const div = $(`#${type}_tbody`)[0];
    div.innerHTML = '';
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
                        div.innerHTML = text['return'];
                    } else {
                        errorText.innerText = 'No search results';
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
//Variables used for the charts div
const canvas = $(`#chart_canvas`)[0];
const ctx = canvas.getContext('2d');
const int = 2;
const letters = "0123456789ABCDEF";
let current = [];
//Function used to get a random color
function getRandomColor(){
    let color = '#';
    for(let i = 0; i < 6; i++){
        color += letters[Math.floor(Math.random()*16)];
    }
    return color;
}
//Function used to draw the chart
function drawChart(data){
    current = [];
    const inner = $('#chart_display')[0];
    inner.innerHTML = '';
    const centerX = canvas.width / int;
    const centerY = canvas.height / int;
    const radius = Math.min(centerX, centerY) * 0.9;
    total = data.reduce((acc, segment) => acc + segment[0], 0);
    let startAngle = 0;
    let colors = [];
    data.forEach(segment => {
        const angle = (segment[0] / total) * int * Math.PI;
        let currentColor = getRandomColor();
        if(colors.includes(currentColor)){ do { currentColor = getRandomColor(); } while (colors.includes(currentColor)); }
        colors.push(currentColor);
        ctx.fillStyle = currentColor;
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, startAngle + angle);
        ctx.lineTo(centerX, centerY);
        ctx.fill();
        startAngle += angle;
        let id = '';
        for(let i = 0; i < 3; i++){
            id += letters[Math.floor(Math.random()*16)];
        }
        inner.innerHTML += `<p id="cc_${segment[1]+id}" filter='false' class='cc-text c-pointer mr-1' onclick='filter("${segment[1]+id}")'><canvas style="width:35px;height:15px;background-color:${currentColor};margin-right:.25rem;"></canvas><span>${segment[1]}</span></p>`;
        current.push([segment[0], segment[1]+id, currentColor]);
    });   
}
//Used to filter out certain values when clicked on
function filter(value){
    const p = document.getElementById(`cc_${value}`);
    if(p.getAttribute('filter') == 'true'){
        p.style = '';
        p.setAttribute('filter', 'false');
    } else {
        p.style = 'text-decoration: line-through;';
        p.setAttribute('filter', 'true');
    }
    const text = document.querySelectorAll('.cc-text');
    data = [];
    text.forEach(function(val, index){
        if(val.getAttribute('filter') == 'false'){
            data.push(current[index]);
        }
    });
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    let startAngle = 0;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(centerX, centerY) * 0.9;
    total = data.reduce((acc, segment) => acc + segment[0], 0);
    data.forEach(segment => {
        const angle = (segment[0] / total) * 2 * Math.PI;
        ctx.fillStyle = segment[2];
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, startAngle + angle);
        ctx.lineTo(centerX, centerY);
        ctx.fill();
        startAngle += angle;
    });
}
const showcd = $(`#show_cd`)[0];
showcd.addEventListener('click', ()=>{
    const content = $(`#chart_content`)[0];
    if(content.style.display == 'none'){
        content.style.display = 'block';
        showcd.innerText = 'Hide chart data';
    } else if(content.style.display == 'block'){
        content.style.display = 'none';
        showcd.innerText = 'Show chart data';
    }
})