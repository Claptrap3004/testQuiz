let questions

const initSelectQuestions = () => {
    questions = JSON.parse(document.querySelector('#jsDataQuestions').getAttribute('data-content'));
    resizeLeftContentSpacer(10);
    createRadioButtons();
    arrangeButtons();
}

const createTableDiv = () => {


    let div = document.createElement('div');
    div.className = "row align-self-center mx-2 pb-2";

    let table = document.createElement('table');
    let row = document.createElement('row');
    let idTh = document.createElement('th');
    let textTh = document.createElement('th');
    let catTh = document.createElement('th');
    row.className = "bold btn btn-primary rounded-3 shadow w-100 ml-3 mr-5 pr-5";

    idTh.innerHTML = 'ID';
    textTh.innerHTML = 'Question - text';
    catTh.innerHTML = 'Category';

    idTh.style.width = '15%';
    textTh.style.width = '78%';
    catTh.style.width = '12%';

    row.appendChild(idTh);
    row.appendChild(textTh);
    row.appendChild(catTh);
    table.appendChild(row);
    table.id = 'questionTable';
    div.appendChild(table);

    return div;
}
const createRadioButtons = () => {
    document.querySelector('#showQuestions').appendChild(createTableDiv());


    for (const question of questions) {
        let element = createRadioButton(question)
        document.querySelector('#questionTable').appendChild(element);
    }
}
const createTableRow = (question) => {

    let tableRow = document.createElement('row');
    let idCol = document.createElement('td');
    let textCol = document.createElement('td');
    let catCol = document.createElement('td');

    idCol.id = 'idCol' + question.id;
    textCol.id = 'textCol' + question.id;
    catCol.id = 'catCol' + question.id;

    idCol.innerHTML = question.id;
    textCol.innerHTML = question.text;
    catCol.innerHTML = question.category;

    idCol.style.width = '10%';
    textCol.style.width = '75%';
    catCol.style.width = '15%';

    tableRow.appendChild(idCol);
    tableRow.appendChild(textCol);
    tableRow.appendChild(catCol);

    return tableRow
}

const createRadioButton = (question) => {

    let div = document.createElement('div');
    let label = document.createElement('label');
    let input = document.createElement('input');


    label.setAttribute('data-bs-toggle', 'button');


    div.className = "row align-self-center mx-3 pb-2";
    label.className = "questionLabels col align-self-center bold btn btn-outline-info rounded-3 shadow btn-lg  mx-3 my-2 p-1 w-100";
    input.className = "btn-check";


    input.id = 'q_id' + question.id;
    label.id = 'ql_id' + question.id;
    label.for = 'ql_id' + question.id;

    div.appendChild(input);
    label.appendChild(createTableRow(question))
    label.addEventListener('click', clickQuestion)
    input.type = 'radio';
    input.name = 'questionId';
    input.autocomplete = 'off';
    input.value = question.id;

    div.appendChild(label);
    // if (question.id === questions[0].id) input.checked = true;
    return div;
}

const clickQuestion = (event) => {
    let idStr = "#q_id" + event.currentTarget.id.replace('ql_id', '');
    let button = document.querySelector(idStr);
    console.log(idStr);
    button.click();
    let labels = document.querySelectorAll('.questionLabels');
    for (const label of labels) {
        if (label === event.currentTarget) continue;
        label.ariaPressed = 'false';
        label.className = "questionLabels col align-self-center bold btn btn-outline-info rounded-3 shadow btn-lg  mx-3 my-2 p-1 w-100";
    }
    let anker = document.querySelector('#selectQuestionToEdit');
    anker.href = 'edit/editQuestion/' + button.value;
    console.log(anker);
}


const arrangeButtons = () => {
    document.querySelector('#divBackToMain').style.left = '20%';
    document.querySelector('#divSelectQuestionToEdit').style.right= '20%';
}
