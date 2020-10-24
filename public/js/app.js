function onClickBtnLike(event) {
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');
    axios.get(url).then(function (respone) {
        spanCount.textContent = respone.data.likes;
        if (icone.classList.contains('fas')) {
            icone.classList.replace('fas', 'far');
        } else {
            icone.classList.replace('far', 'fas');
        }
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous êtes pas encore connectés");
        } else {
            window.alert("Une erreur s'est produite ...");
        }
    });
}
document.querySelectorAll('a.js-likes').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike)
})