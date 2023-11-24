import "../css/app.scss"

require('bootstrap')


document.addEventListener("submit", function (e) {
    e.preventDefault();

    sendFormData();
})

function sendFormData() {
  

  let xhr = new XMLHttpRequest();
  let form = document.getElementById('form');
  let formData = new FormData(form);

  xhr.open('POST', 'amo', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        document.querySelector('.alert.alert-success').style.display = "block";
        form.reset(); 
        window.setTimeout(function(){}, 1000);
        window.setTimeout(function(){
            document.querySelector(".alert.alert-success").style.display = "none";
        }, 5000);
      } else {
        document.querySelector('.alert.alert-danger').style.display = "block";
        window.setTimeout(function(){
            document.querySelector(".alert.alert-danger").style.display = "none";
        }, 5000);
      }
    }
  };

  xhr.send(formData);
}
