//Function used to sort table data, dependant on what header was clicked
function headerClicked(string, integer){
    //set the headers of the table to contain the correct arrow showing how the table is sorted
    const headers = $(`#${string}_thead`).find('tr:first th');
    let order = 'asc';
    for(let i = 0; i < headers.length; i++){
        if(i === integer){
            if(headers[i].getAttribute('sort') === 'asc'){
                headers[i].setAttribute('sort', 'desc');
                headers[i].querySelector('span').innerHTML = '&darr;';
                order = 'desc';
            } else if(headers[i].getAttribute('sort') === 'desc'){
                headers[i].setAttribute('sort', 'asc');
                headers[i].querySelector('span').innerHTML = '&uarr;';
            } else if(headers[i].getAttribute('sort') === ''){
                headers[i].querySelector('span').innerHTML = '&uarr;';
                headers[i].setAttribute('sort', 'asc');
            }
        } else {
            headers[i].setAttribute('sort', '');
            headers[i].querySelector('span').innerHTML = '';
        }
    }
    //Get all the table data and put it into a array, and only performs the task if there is data within the table to sort
    const body = $(`#${string}_tbody`).find('tr');
    if(body.length != 0){
        let array = [];
        body.each(function(index, row){
            const tds = $(row).find('td');
            let tmpArray = [];
            tds.each(function(tdindex, td){
                if(/[0-9]/.test(td.innerText) === true && td.innerText.includes('/') === true && /[a-zA-Z]/.test(td.innerText) === false){
                    tmpString = td.innerText.split('/');
                    tmpArray.push([new Date(`${tmpString[1]}/${tmpString[0]}/${tmpString[2]}`).getTime(), 'date']);
                } else if(td.innerText.includes('N/A')){
                    tmpArray.push([0, 'date']);
                } else if(td.querySelector('a')){
                    tmpArray.push([td.innerText, td.querySelector('a').getAttribute('href')]);
                } else if(td.querySelector('input')){
                    const input = td.querySelector('input');
                    tmpArray.push([[input.getAttribute('uid'), input.getAttribute('changed'), input.value], 'input']);
                } else{
                    tmpArray.push([td.innerText, null]);
                }
            })
            const tmpData = tmpArray[0];
            tmpArray[0] = tmpArray[integer];
            tmpArray[integer] = tmpData;
            array.push(tmpArray);
        });
        //Sorts the data in the array
        if(order === 'asc'){
            if(/[0-9]/.test(array[0][0]) === true && /[a-zA-Z]/.test(array[0][0]) === false){
                array.sort(function(a,b){return parseFloat(a[0]) - parseFloat(b[0])});
            } else{
                array.sort(function(a,b){
                    const x = a[0][0];
                    const y = b[0][0];
                    if(x < y){return -1;}
                    if(y < x){return 1;}
                    return 0;
                });
            }
        } else if(order === 'desc'){
            array.reverse();
        }
        //Rearrange the array to the default arrangement
        let sortedArray = [];
        array.forEach(function(element){
            const tmpData = element[integer];
            element[integer] = element[0];
            element[0] = tmpData;
            sortedArray.push(element);
        });
        //Add the data back to the table
        const tbody = $(`#${string}_tbody`)[0];
        tbody.innerHTML = '';
        sortedArray.forEach(function(element){
            let row = '<tr>';
            for(let i = 0; i < element.length; i++){
                if(element[i][1] === 'date'){
                    row += (element[i][0] > 0) ? `<td>${(new Date(element[i][0])).toLocaleDateString('en-GB')}</td>` : `<td>N/A</td>`;
                } else if(element[i][1] === 'input'){
                    row += `<td><input class='update-company' value='`+element[i][0][2]+`' changed='`+element[i][0][1]+`' uid='`+element[i][0][0]+`' onchange='changed_company(this)'></td>`
                } else if(element[i][1] !== null){
                    row += `<td><a href='window.location.href=`+element[i][1]+`' target='_blank'>${element[i][0]}</a></td>`;
                } else {
                    row += `<td>${element[i][0]}</td>`;
                }
            }
            row += '</tr>';
            tbody.innerHTML += row;
        });
    }
}