let editData;
let categories;
let answers;

const initEditQuestion = () => {
    editData = JSON.parse(document.querySelector('#jsDataEditQuestions').getAttribute('data-content'));
    categories = JSON.parse(document.querySelector('#jsDataEditCategories').getAttribute('data-content'));
    answers = [...editData.rightAnswers,...editData.wrongAnswers];
    for (let i = answers.length; i < 4; i++) {
        answers.push({id:null,text:''})
    }
    document.querySelector('#editCategoryText').style.visibility = 'hidden';
    // resizeLeftContentSpacer(10);
    createSelectOptions();
    createInputFields();
    addListeners();
    fillInputFields();
}

const addListeners = () => {
    document.querySelector('#editCategory').addEventListener('change',toggleInputVisible);
    document.querySelector('#confirmEditChanges').addEventListener('click',createAnswerObjectArray);

}

const toggleInputVisible = (event) => {

    let toToggle = document.querySelector('#editCategoryText');
    if (Number(event.target.value) === 0) toToggle.style.visibility = 'visible';
    else toToggle.style.visibility = 'hidden';
}

const createSelectOptions = () => {
    for (const category of categories) {
        let categorySelect = document.createElement('option');
        if (category.id === editData.category.id) categorySelect.selected = true;
        categorySelect.value = category.id;
        categorySelect.innerHTML = category.text;
        document.querySelector('#editCategory').appendChild(categorySelect);
    }
}


const createInputFields = () =>{
    for (let i = 0; i < answers.length; i++) {
        createAnswerInputField(i)
    }
}

const createAnswerInputField = (index) => {
    let div = document.createElement('div');
    let label = document.createElement('label');
    let checkbox = document.createElement('input');
    let field = document.createElement('textarea');

    // checkbox.name = `answerCheckBox[]`;
    // field.name = `answerText[]`;

    div.id = "answerDiv" + index;
    checkbox.id = 'answerCheckBox' + index;
    field.id = "answerText" + index;

    div.className = 'editDiv ';
    checkbox.className = 'editCheckBox mx-3 '
    field.className = "editText ";

    checkbox.type = "checkbox";
    field.cols=100;
    field.rows=1;
    field.wrap="hard";
    field.maxLength=255;

    label.appendChild(field);
    div.appendChild(label);
    div.appendChild(checkbox);

    document.querySelector('#editAnswersDiv').appendChild(div);
}

const fillInputFields = () => {
    fillQuestionField();
    for (let i = 0; i < answers.length; i++) {
        fillAnswerField(i);
    }
}

const fillQuestionField = () => {
    document.querySelector('#editQuestionId').value = editData.id;
    document.querySelector('#editQuestionText').value = editData.text;
    document.querySelector('#editQuestionExplanation').value = editData.explanation;
}

const fillAnswerField = (index) => {
    let checkboxId = '#answerCheckBox' + index;
    let textInputId = "#answerText" + index;
    let checkbox = document.querySelector(checkboxId);
    let textInput = document.querySelector(textInputId);
    let answer = answers[index];

    checkbox.value = answer.id;
    checkbox.checked = isRightAnswer(answer.id);
    textInput.value = answer.text;
}

const isRightAnswer = (id) =>{
    for (const rightAnswer of editData.rightAnswers) {
        if (rightAnswer.id === id) return true
    }
    return false
}

const createAnswerObjectArray = () => {
    class answer{
        constructor(id,text, isRight){
            this.id = id;
            this.text = text;
            this.isRight = isRight;
        }
    }

    console.log('clicked');
    let answers = [];
    let editedAnswers = document.querySelectorAll('.editDiv');
    for (let index = 0; index < editedAnswers.length; index++) {
        let checkboxId = '#answerCheckBox' + index;
        let textInputId = "#answerText" + index;
        let checkbox = document.querySelector(checkboxId);
        let textInput = document.querySelector(textInputId);
        answers.push(new answer(checkbox.value, textInput.value,checkbox.checked))
    }
    document.querySelector('#answerArrayJSON').value = JSON.stringify(answers);
    document.querySelector('#confirmEditQuestion').click();
}