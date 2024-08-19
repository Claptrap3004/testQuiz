const init = () => {
    document.querySelector('#navQuickStart').addEventListener('click', quick20);
}

const changeModal = (title, text, confirmFunction) => {
    console.log('modal set')
    let confirmButton = document.querySelector('#modalConfirm');
    let clone = confirmButton.cloneNode(true);
    confirmButton.replaceWith(clone);
    document.querySelector('#modalTitle').innerHTML = title;
    document.querySelector('#modalText').innerHTML = text;
    document.querySelector('#modalConfirm').addEventListener('click', confirmFunction);
}

const removeLeftContentSpacer = () => {
    document.querySelector('#spacerContentLeft').remove();
}

const resizeLeftContentSpacer = (percentage) => {
    let spacer = document.querySelector('#spacerContentLeft');
    document.querySelector('#spacerContentLeft').className = "col-4";
    spacer.style.minWidth= percentage + '%';
    spacer.style.maxWidth= percentage + '%';
}

const quick20 = () => quickstart(20);
const quick50 = () => quickstart(50);

const quickstart = (numberOfQuestions) => {
    let xhttp = new XMLHttpRequest();
    let url = `/quizQuestion/quickStart?numberOfQuestions=${numberOfQuestions}`;
    xhttp.open("GET", url,true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
    document.querySelector('#directToAnswers').click();
}