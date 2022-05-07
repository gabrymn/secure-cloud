document.getElementById('ID_INPUT_FILE').onchange = async (e) => {

    const files = Object.values(e.target.files);
    files.forEach((file) => {
        genArrayBuffer(file)
            .then((r) => {
                console.log(r);
                var blob = new Blob([r]);
                const link = document.createElement('a');
                link.style.display = 'none';
                document.body.appendChild(link);  
                link.href = x;
                link.download =  e.target.files[i].name;
                link.click();
            })
            .catch((error) => {
                alert("errore, file troppo grande")
            })
    })
}

const genArrayBuffer = async (file) => new Promise((resolve, reject) => {
    var fr = new FileReader();
    fr.readAsArrayBuffer(file)
    fr.onload = () => resolve(fr.result);
    fr.onerror = error => reject(error);
})