const initLogin = () => {

    let contentLeft = document.querySelector('#contentLeft');
    let errorData = document.querySelector('#jsLoginErrorData').getAttribute('data-content');
    document.querySelector('#spacerContentLeft').className = "col-4";
    contentLeft.style.minWidth='40%';
    contentLeft.style.maxWidth='40%';
    document.querySelector('#contentRight').className = 'login-contentRight';
    document.querySelector('#loginRegisterToggleButton').addEventListener('click', toggleLoginRegister);
    document.querySelector('#loginRegisterConfirmButton').style.left = '20%';
    document.querySelector('#loginRegisterToggleButton').style.right= '20%';
    if (errorData !== '') handleErrors(errorData);

}

const handleErrors = (errorData) => {

    const errorObject = JSON.parse(errorData);
    if (!errorObject.isLoginError) document.querySelector('#loginRegisterToggleButton').click();
    for (const errorObjectKey in errorObject) {
        if (errorObjectKey === 'isLoginError') continue;
        if (errorObject[errorObjectKey] !== null){
            let inputId = 'input' + capitalize(errorObjectKey);
            showError(inputId, errorObject[errorObjectKey])
        }
    }
    console.log(errorObject);
}

const showError = (id, errorObject) => {
    let inputField = document.querySelector('#'+ id);
    let divId = '#div' + capitalize(id);
    if (errorObject.errorMessage !== ''){
        let hint = document.createElement('div');
        hint.className = "invalid-feedback";
        hint.innerHTML = errorObject.errorMessage;
        inputField.className += ' is-invalid';
        inputField.addEventListener('click', removeError);
        document.querySelector(divId).appendChild(hint);

    }
    inputField.value = errorObject.input;
}

const removeError = (event) => {
    console.log(event.currentTarget);
    let input = event.currentTarget;
    let divId = '#div' + capitalize(input.id);
    let div = document.querySelector(divId);
    if (div.lastChild !== event.currentTarget) div.removeChild(div.lastChild);
    input.className = input.className.replace(' is-invalid', '');
    input.removeEventListener('click', removeError);
}

const capitalize = (s) =>
{
    return s[0].toUpperCase() + s.slice(1);
}

const setLoginScreen = () => {
    let newContentLeft = document.createElement('div');
    newContentLeft.className = "col-6 bg-light rounded-5 my-2 py-2 align-self-left scrollable-contentleft";
    document.querySelector('#spacerContentLeft').className = "col-2";
    document.querySelector('#contentRight').hidden = 'hidden';
    document.querySelector('#contentLeft').replaceWith(newContentLeft);
    console.log(document.querySelector('#contentLeft').className);
}

const toggleLoginRegister = (event) => {
    let buttonUserName = document.querySelector('#inputUsername');
    let buttonEmailValidate = document.querySelector('#inputEmailValidate');
    let buttonPasswordValidate = document.querySelector('#inputPasswordValidate');
    let labelUserName = document.querySelector('#labelInputUsername');
    let labelEmailValidate = document.querySelector('#labelInputEmailValidate');
    let labelPasswordValidate = document.querySelector('#labelInputPasswordValidate');
    let buttonConfirm = document.querySelector('#loginRegisterConfirmButton');

    if (buttonUserName.type === 'hidden') {
        buttonUserName.type = 'text';
        buttonEmailValidate.type = 'email';
        buttonPasswordValidate.type = 'password';
        labelUserName.style.visibility = "visible";
        labelEmailValidate.style.visibility = "visible";
        labelPasswordValidate.style.visibility = "visible";
        buttonConfirm.name = 'registerUser';
        buttonConfirm.innerHTML = 'Sign Up';
        event.target.innerHTML = 'go to Login';
    } else {
        buttonUserName.type = 'hidden';
        buttonEmailValidate.type = 'hidden';
        buttonPasswordValidate.type = 'hidden';
        labelUserName.style.visibility = "hidden";
        labelEmailValidate.style.visibility = "hidden";
        labelPasswordValidate.style.visibility = "hidden";
        buttonConfirm.name = 'loginUser';
        buttonConfirm.innerHTML = 'Sign In';
        event.target.innerHTML = 'go to Register';
    }
}
