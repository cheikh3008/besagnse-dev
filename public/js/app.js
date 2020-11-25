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
  
// document.querySelectorAll("#form-comment").forEach(function(btn_form) {
//     btn_form.addEventListener('submit', function onClickBtnComment (event) {
//     event.preventDefault();
//     var data_form = new FormData(this);
//     var http = new XMLHttpRequest();
//     http.onreadystatechange = function () {
//         if (this.readyState == 4 && this.status == 200) {
            
//             var res = http.response;
//             //console.log(res);
//             // for (let i = 0; i < res.length; i++) {
//             //     const element = res[i];
//             //     console.log(element.message + "  " + element.prenom);
//             //     document.querySelector("#message-com").append(element.message);
//             //     document.querySelector("#fullname").append( element.prenom + " " + element.nom);
//             // }
//             http.onload = function () {
//               const res = getData();
//             };
//             var input = document.querySelectorAll("textarea");
//             for (var i = 0; i < input.length; i++) {
//                 input[i].value = '';
//             }
            
//         } else if (this.readyState == 4 && this.status == 403) {
//             window.alert(
//                 "Permission non accordée car vous êtes pas encore connectés"
//             );
//         }
//     };
//     var url = this.getAttribute("action");
//     http.open("POST", url, true);
//     http.responseType = 'json'
//     http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
//     http.send(data_form);

//     return false;
//     })
    
    
// });

// //afficher les commentaires au format json
// var getData = function(){
//     $(document).ready(function () {
//       $.ajax({
//         url: "/",
//         type: "GET",
//         dataType: "json",
//         async: true,

//         success: function (data) {
            
//             const msg = document.querySelectorAll("#commentaires").forEach(function (test) {
//                 console.log(test.nextElementSibling.action);

//             });
//             const html = data.reverse().map(function(message){
//                 return `
                
//                 <div id="commentaires">

//                     <div class="badge badge-pill badge-light mt-2 mb-2 text-left"  >
//                         <small class="font-weight-bold " id="fullname" style="font-size: 1em;"> 
//                             ${message.fullname}
//                         </small><br>
//                         <small class="small  text-small " id="message-com"  style="font-size: 1em;">
//                             ${message.message}
//                         </small>
//                         </div>
//                     </div> 
 
//                 `;
               
//             }).join('');
//             //resultat.innerHTML = html;
//             //resultat.scrollTop = message.scrollHeight;
//             document.querySelectorAll("#commentaires").forEach(function(resultat){
//                 resultat.innerHTML = html;
//                 resultat.innerHTML = html;
                
//             });
//         },
//         error: function (xhr, textStatus, errorThrown) {
//           alert("Ajax request failed.");
//         },
//       });
//     });
// }
// getData();