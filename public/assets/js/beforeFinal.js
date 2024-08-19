let statsData;
let questions;
const initBeforeFinal = () => {
    statsData = JSON.parse(document.querySelector('#jsBeforeFinalData').getAttribute('data-content'));
    removeLeftContentSpacer();
    questions = statsData.questions;

    for (let i = 0; i < questions.length; i++) {
        addBeforeFinalButton(questions[i].id, i);
        console.log(checkAnswersGiven(i));
    }
}

const addBeforeFinalButton = (questionId, index) => {
    let newButton = document.createElement('label');
    let addClass = checkAnswersGiven(index) ? 'btn-success' : 'btn-info';
    newButton.className = 'beforeFinalButton btn-lg shadow m-2 p-2 ' + addClass;
    newButton.value = index;
    newButton.innerHTML = questionId;
    document.querySelector('#beforeFinalButtons').appendChild(newButton);
}

const checkAnswersGiven = (index) => {
    return questions[index].givenAnswers.length;
}