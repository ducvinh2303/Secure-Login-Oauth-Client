/**.====================================.
 * | REGISTRATION JAVASCRIPT VALIDATION |
 * '===================================='
 * 
 * Resources used:
 * regexr: https://regexr.com for providing an environment to create the regex expressions used in the validation
 * W3Schools: https://www.w3schools.com/js/default.asp for providing excellent documentation and an online JS environment
 */
const form = document.forms.registrationForm;
console.log(form);
if (form) {
    console.log('Registration Validator up and running :)')
    form.addEventListener('submit', validateForm);
} else {
    console.error('Unable to find the registration form; no validation will be performed');
}

function resetForm() {
    for (i = 0; i < 5; i++){
        form[i].value = null;
    }
    document.getElementById("error").innerText = ""
}

function validateForm(event) {
    const u_name_invalid_chars = /([^!@#$%^&*()\-+_=a-zA-Z0-9 ])/
    const password_invalid_chars = /([^!@#$%^&*()\-+_=a-zA-Z0-9])/
    const email_chars = /(^[^@]+$)|(@{2,})/

    if (form.f_name.value.length < 3) {
        document.getElementById("error").innerText = "First name must have a minimum of 3 characters"
        event.preventDefault();
    }
    else if (form.f_name.value.length > 24) {
        document.getElementById("error").innerText = "First name must have a maximum of 24 characters"
        event.preventDefault();
    }
    else if (form.u_name.value.length < 3) {
        document.getElementById("error").innerText = "Username must have a minimum of 3 characters"
        event.preventDefault();
    }
    else if (form.u_name.value.length > 24) {
        document.getElementById("error").innerText = "Username must have a maximum of 24 characters"
        event.preventDefault();
    }
    else if (u_name_invalid_chars.test(form.u_name.value)) {
        document.getElementById("error").innerText = "Username can only contain\nA-Z, a-z, 0-9\n!@#$%^&*()_+-=\nand Space"
        event.preventDefault();
    }
    else if (form.email.value.length < 1) {
        document.getElementById("error").innerText = "Email address must have a minimum of 1 character"
        event.preventDefault();
    }
    else if (form.email.value.length > 999) {
        document.getElementById("error").innerText = "Email address must have a maximum of 999 character"
        event.preventDefault();
    }
    else if (email_chars.test(form.email.value)) {
        document.getElementById("error").innerText = "Email address must contain one and only one @"
        event.preventDefault();
    }
    else if (form.password.value.length < 8) {
        document.getElementById("error").innerText = "Password must have a minimum of 8 characters"
        event.preventDefault();
    }
    else if (form.password.value.length > 30) {
        document.getElementById("error").innerText = "Password must have a maximum of 30 characters"
        event.preventDefault();
    }
    else if (password_invalid_chars.test(form.password.value)) {
        document.getElementById("error").innerText = "Password can only contain\nA-Z, a-z, 0-9\n!@#$%^&*()_+-=\n"
        event.preventDefault();
    }
    else if (form.c_password.value != form.password.value) {
        document.getElementById("error").innerText = "Passwords must match"
        event.preventDefault();
    }
    else if (!form.checkbox.checked) {
        document.getElementById("error").innerText = "Please tick the checkbox"
        event.preventDefault();
    }
}