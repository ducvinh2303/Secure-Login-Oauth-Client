/**.=============================.
 * | LOGIN JAVASCRIPT VALIDATION |
 * '============================='
 * 
 * Resources used:
 * regexr: https://regexr.com for providing an environment to create the regex expressions used in the validation
 * W3Schools: https://www.w3schools.com/js/default.asp for providing excellent documentation and an online JS environment
 */
const form = document.forms.loginForm;
console.log(form);
if (form) {
    console.log('Login Validator up and running :)')
    form.addEventListener('submit', validateForm);
} else {
    console.error('Unable to find the login form; no validation will be performed');
}

function validateForm(event) {

    if(form.u_name.value.length < 1) {
        document.getElementById("error").innerText = "Username cannot be blank"
        event.preventDefault();
    }
    else if(form.password.value.length < 1) {
        document.getElementById("error").innerText = "Password cannot be blank"
        event.preventDefault();
    }
}