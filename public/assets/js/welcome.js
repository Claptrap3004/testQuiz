const initWelcome = () => {
    document.querySelector('#clearAllStatsButton').addEventListener('click', clearAllStats)
    document.querySelector('#quickstart20').addEventListener('click', quick20)
    document.querySelector('#quickstart50').addEventListener('click', quick50)
    resizeLeftContentSpacer(10);
    checkQuizExists().then(r => directToAnswerScreen()).catch();
}
const clearAllStats = () => {
    changeModal('Löschen aller Stats', 'Durch bestätigen werden alle Stats gelöscht', confirmDeleteStats)
}

const confirmDeleteStats = () => {
    document.querySelector('#allTimesAsked').innerHTML = '0';
    document.querySelector('#allTimesRight').innerHTML = '0';
    document.querySelector('#allRate').innerHTML = '0';
    document.querySelector('#closeModal').click();
    deleteAllStats();
}

const deleteAllStats = () => {
    let xhttp = new XMLHttpRequest();
    xhttp.open("GET", "/quizQuestion/deleteStatsAll", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

const checkQuizExists = async function (){
    return await quizExists();
}
const quizExists = () => {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "/quizQuestion/quizExists", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = () => {

           if (Number(xhr.responseText) > 0) resolve(true);
           else reject(false);
        };
        // needs to be fixed
        xhr.onerror = () => reject(false);
        xhr.send();
    });


}

