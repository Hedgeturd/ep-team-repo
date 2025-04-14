let reg;    //True = Registering
//var mysql = require('mysql');

function dbcon(user, pass) {
    alert('Hello there ' + user + ', I am being submitted');
    alert('Your Password is ' + pass);
}

function loginFunction() {

    let x = document.forms['loginForm']["user"].value;
    let y = document.forms['loginForm']["pass"].value;

    if (!x || !y) {
        alert('Please fill username and password fields');
        return;
    }

    // DEBUG ONLY ALERTS
    if (!reg) {
        alert("YOU ARE USING LOGIN MODE")
    }
    if (reg) {
        alert("YOU ARE USING SIGNUP MODE")
    }

    dbcon(x, y);
    window.open("./SignedIn/");
}