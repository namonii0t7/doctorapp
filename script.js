

function myMenuFunction() {
    var i = document.getElementById("navMenu");
    if (i.className === "nav-menu") {
        i.className += " responsive";
    } else {
        i.className = "nav-menu";
    }
}

var a = document.getElementById("loginBtn");
var b = document.getElementById("registerBtn");
var x = document.getElementById("login");
var y = document.getElementById("register");

function login() {
    x.style.left = "4px";
    y.style.right = "-520px";
    a.className += " white-btn";
    b.className = "btn";
    x.style.opacity = 1;
    y.style.opacity = 0;
}

function register() {
    x.style.left = "-510px";
    y.style.right = "5px";
    a.className = "btn";
    b.className += " white-btn";
    x.style.opacity = 0;
    y.style.opacity = 1;
}




 window.onload = function () {
    const params = new URLSearchParams(window.location.search);
    const action = params.get("action");

    if (action === "register") {
      register();  // call your existing function
    } else {
      login();     // default to login view
    }
  };

document.getElementById('editBtn').addEventListener('click', function () {
  const inputs = document.querySelectorAll('input');
  inputs.forEach(input => {
    if (input.name !== 'email' && input.type !== 'hidden') {
      input.removeAttribute('readonly');
    }
  });
  document.getElementById('saveBtn').style.display = 'inline-block';
});


 function myMenuFunction() {
  const menu = document.getElementById("mobileMenu");
  menu.classList.add("show");
}

function closeMobileMenu() {
  const menu = document.getElementById("mobileMenu");
  menu.classList.remove("show");
}



