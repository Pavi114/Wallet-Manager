var register = document.querySelector("#register");
var login = document.querySelector("#login");
var hasAccount = document.querySelector("#showRegister");
var showLogin = document.querySelector("#showLogin");
var hidden = document.querySelector(".hidden");



hasAccount.addEventListener("click",function(){
	register.classList.remove("hidden");
	login.classList.add("hidden");
});

showLogin.addEventListener("click",function(){
	register.classList.add("hidden");
	login.classList.remove("hidden");
})

