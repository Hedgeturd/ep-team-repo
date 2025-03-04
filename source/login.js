let reg;    //True = Registering

function dbcon(user, pass) {
    alert('Hello there ' + user + ', I am being submitted');
    alert('Your Password is ' + pass);
}

void function loginFunction() {
    // DEBUG ONLY ALERTS
    if (!reg) {
        alert("YOU ARE USING LOGIN MODE")
    }
    if (reg) {
        alert("YOU ARE USING SIGNUP MODE")
    }

    const nameValue = document.getElementById("user").value;
    const passValue = document.getElementById("pass").value;

    dbcon(nameValue, passValue);
    window.open("dashboard.html");
}