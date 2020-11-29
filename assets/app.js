/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import $ from 'jquery';
import 'bootstrap';
import axios from 'axios';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/app.js');
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
//Gestion des commentaire avec XMLHttpRequest
const form = document.querySelectorAll('form.form-comment');
for (let j = 0; j < form.length; j++) {
    const element = form[j];
    element.addEventListener('submit', function(event){
        event.preventDefault();
        console.log(element);
        var data_form = new FormData(element);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(JSON.parse(xhr.responseText));
                xhr.onload = function () {
                    var input = element.querySelector("textarea");
                    input.value = "";
                    input.focus();
                    setInterval(function(){
                        location.reload();
                    }, 100)
                    
                };
                
            }else if (this.readyState == 4 && this.status == 403) {
                window.alert("Permission non accordée car vous êtes pas encore connectés" );
                var input = element.querySelector("textarea");
                input.value = "";
                input.focus();
            }
        }
        var url = this.getAttribute("action");
        xhr.open("POST", url, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(data_form);
    });
}