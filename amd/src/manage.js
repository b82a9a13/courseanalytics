const sorh = $(`#showorhide`)[0];
sorh.addEventListener('click', ()=>{
    const list = $('#courselist')[0];
    list.style.display = (list.style.display === 'none') ? 'block' : 'none';
    sorh.innerText = (sorh.innerText.includes('Show')) ? sorh.innerText.replace('Show','Hide') : sorh.innerText.replace('Hide','Show');
});