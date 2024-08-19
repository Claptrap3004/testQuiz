const initWelcome = () => {
    document.querySelector('#clearAllStatsButton').addEventListener('click', clearAllStats)
    document.querySelector('#quickstart20').addEventListener('click', quick20)
    document.querySelector('#quickstart50').addEventListener('click', quick50)
    resizeLeftContentSpacer(10);
    let exists = quizExists().then(() => {
        return true}).catch(() => {return false});
    if (exists) document.querySelector('#directToAnswers').click();
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
const quizExists = () => {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "/quizQuestion/quizExists", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = () => {
            console.log(xhr.responseText)
           if (xhr.responseText !== '') resolve(true);
           else reject(false);
        };
        xhr.onerror = () => reject(false);
        xhr.send();
    });

}

