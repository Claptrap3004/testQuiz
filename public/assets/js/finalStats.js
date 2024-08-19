let statsData;
let questions;
const initFinalStats = () => {
    statsData = JSON.parse(document.querySelector('#jsStatsData').getAttribute('data-content'));
    questions = statsData.questions;
    resizeLeftContentSpacer(10);
    for (let i = 0; i < questions.length; i++) {
        addStatsButton(questions[i].id,i)
    }
    addStats();

}
const addStats = () => {
    document.querySelector('#finalStatsAsked').innerHTML = statsData.questionsAsked;
    document.querySelector('#finalStatsRight').innerHTML = statsData.answeredRight;
    document.querySelector('#finalStatsRate').innerHTML = statsData.rate;
}

const addStatsButton = (questionId,index) => {
    let newButton = document.createElement('label');
    let addClass = Number(questions[index].answeredCorrect) ? 'btn-success' : 'btn-danger';
    newButton.className = 'statsButton btn-lg shadow m-2 p-2 ' + addClass;
    newButton.value = index;
    newButton.innerHTML = questionId;
    newButton.addEventListener('mouseover',showDetails);
    newButton.addEventListener('mouseleave',hideDetails);
    document.querySelector('#statsButtons').appendChild(newButton);

}

const showDetails = (event) => {
    let question = questions[Number(event.target.value)];

    document.querySelector('#details').removeAttribute('hidden');
    document.querySelector('#detailsQuestion').innerHTML = question.text;
    document.querySelector('#detailsDescription').innerHTML = question.explanation;
    let detailsAnswers = document.querySelector('#detailsAnswers');
    detailsAnswers.innerHTML = document.createElement("p").innerHTML = '';
    let answers = question.rightAnswers.concat(question.wrongAnswers);
    for (let answer of answers) {
        let nextNode = document.createElement("p");
        nextNode.innerHTML = answer.text;
        nextNode.style.textAlign = 'center';
        nextNode.style.color = answerInArray(answer.id, question.rightAnswers) ? 'green' : 'red';
        nextNode.style.background = answerInArray(answer.id, question.givenAnswers) ? 'yellow' : detailsAnswers.style.background;
        detailsAnswers.appendChild(nextNode);
    }
}
const hideDetails = () => {
    document.querySelector('#details').hidden = 'hidden';

}

const answerInArray = (answerId, answersArray) => {
    let isIn = false;
    for (const answersArrayElement of answersArray) {
        if (answersArrayElement.id === answerId) isIn = true;
    }
    return isIn;
}
