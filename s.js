const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

// script.js
document.getElementById('login').addEventListener('click', function() {
    document.querySelector('.sign-in').style.display = 'block';
    document.querySelector('.sign-up').style.display = 'none';
});

document.getElementById('register').addEventListener('click', function() {
    document.querySelector('.sign-up').style.display = 'block';
    document.querySelector('.sign-in').style.display = 'none';
});
