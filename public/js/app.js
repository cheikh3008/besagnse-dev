//Fonction qui gére le button jaime
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
   return false;
}
document.querySelectorAll('a.js-like').forEach(function(link){
    link.addEventListener('click', onClickBtnLike )
});
//Fonction qui gére les commentaires

document.querySelectorAll("#form-comment").forEach(function(btn_form) {
    btn_form.addEventListener('submit', function onClickBtnComment (event) {
    event.preventDefault();
    var data_form = new FormData(this);
    console.log(this);
    console.log(data_form);
    var http = new XMLHttpRequest();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.response);
            var input = document.querySelectorAll("textarea");
            for (var i = 0; i < input.length; i++) {
                input[i].value = "";
            }
            
            
        } else if (this.readyState == 4 && this.status == 403) {
            window.alert(
                "Permission non accordée car vous êtes pas encore connectés"
            );
        }
    };
    console.log(this);
    var url = this.getAttribute("action");
    http.open("POST", url, true);
    http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    http.send(data_form);

    return false;
    })
});

