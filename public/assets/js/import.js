const initImport = () => {
    createFileChoiceDiv();
}

const createFileChoiceDiv = () => {
    let div = document.createElement('div');
    let label = document.createElement('label');
    label.className = 'form-label';
    let input = document.createElement('input');
    input.className = "form-control";
    input.type = 'file';
    input.id = 'importFile';
    input.addEventListener('change', uploadFile)
    label.appendChild(input);
    div.appendChild(label);
    document.querySelector('#importLeftContent').appendChild(div);
}


const uploadFile = (event) => {
    let dest = document.querySelector('#header');
    let formData = new FormData();
    formData.append('file',event.target.files[0]);

    fetch("", {
        method: "POST",
        body: formData
    }).then(r => backToMain).catch(e => errorReport).finally(backToMain)
}
const errorReport = () => {
    let anker = document.createElement('a');
    anker.href = 'edit/import/';
    anker.click();

}

const backToMain = () => {
    let anker = document.createElement('a');
    anker.href = 'index';
    anker.click();
}
