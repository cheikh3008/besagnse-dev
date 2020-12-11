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
                        //getMessage()
                    }, 600)
                };
                
            }else if (this.readyState == 4 && this.status == 403) {
                window.alert("Permission non accordée car vous êtes pas encore connectés" );
                var input = element.querySelector("textarea");
                input.value = "";
                input.focus();
            } else if (this.readyState == 4 && this.status == 500) {
                window.alert("Ce champ ne peut pas être vide . ");
            }
        }
        var url = this.getAttribute("action");
        xhr.open("POST", url, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(data_form);
    });
}
// Afficher les commentaires d'un pin par lot de 3

$(function () {
    if($(".msg").length == 0){
        $("#load_more").remove()
    }
    $(".msg").slice(3).hide()
    $("#load_more").on('click', function (e) {
        e.preventDefault()
        $(".msg:hidden").slice(0,3).slideDown()
        if($(".msg:hidden").length == 0){
            $("#load_more").remove()
            
        }
    })
})


// Afficher tous les commentaires d'un pin par lot de 3
// var msg = $("[class*='msg-index-']")
// console.log( msg);
// // for (let i = 0; i < msg.length; i++) {
// //     const element = msg[i];
// //     console.log( element);
// // }
$("[class*='msg-index-']").slice(3).hide()
$(document).on('click', '#load_more_index', function (e) {
    e.preventDefault()
    var id = $(this).data('id')
    $(".msg-index-"+ id+ ":hidden").slice(0, 3).slideDown()
    if($(".msg-index-"+ id+ ":hidden").length == 0){
       $(this).remove()
        
    }
    
})


// $(function () {
//     $(".msg-index-23").slice(3).hide()
//     $("#load_more_index").data('id').on('click', function (e) {
//         e.preventDefault
//         var id = $("#load_more_index").data('id')

//         $(".msg-index-"+ id+ ":hidden").slice(0, 3).slideDown()
//         if($(".msg-index-"+ id+ ":hidden").length == 0){
//             $("#load_more_index").remove()
        
//         }
//     })
// })


// $(document).ready(function (){
//     getMessage()
// })
// getMessage()
// function getMessage(){
//     $.ajax({
//         url: '',
//         type: 'GET',
//         dataType: 'json',
//         async: true,
//         success: function(data){
//             for (let i = 0; i < data.length; i++) {
//                 const element = data[i];
//                 var e = $('<div class="badge badge-pill badge-light mt-2 mb-2 text-left"><small id= "fullname" class=""></small> <br> <small id="message"></small></div><br>')
//                 $("#fullname", e).html(element['fullname'])
//                 $("#message", e).html(element['message'])
//                 $("#msg").append(e)
//                 //console.log(e);
//             }
//         }, error : function (error) {
//             console.log(error);
//         }
//     })
// }

