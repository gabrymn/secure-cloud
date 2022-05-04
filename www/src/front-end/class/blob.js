/*
<body>
    <input id="inp" type='file'> // input file
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
<script>
    document.getElementById("inp").addEventListener("change", FILE_URL.READ_FILE, false);
</script>
*/

export default class FILE_URL {

    static B64_2_BLOB = (b64data, filename) => {
        const mime = FILE_URL.#GET_MIME(b64data);
        var file = FILE_URL.#DATAURL_2_FILE(b64data, filename);
        const blob = new Blob([file], {type: mime});
        return blob;
    }

    static GET_BLOB_URL = (blob) => URL.createObjectURL(blob)

    static #DATAURL_2_FILE = (dataurl, filename) => {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, {type:mime});
    }

    static #GET_MIME = (dataurl) => {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        return arr[0].replace("data:", "").replace(";base64", "");
    }

    static READ_FILE() {
        if (this.files && this.files[0]) {
            var fr = new FileReader();
            fr.addEventListener("load", (event) => {
                const filename = this.files[0].name;
                const b64data = event.target.result;
                const blob = FILE_URL.B64_2_BLOB(b64data, filename);
                sessionStorage.setItem("filename", filename);
                sessionStorage.setItem("data", b64data);
                var inner = '<div style="border: 2px solid black;"><img src='+b64data+'><a href='+URL.createObjectURL(blob)+' download='+filename+'>Download</a></div>';
                document.body.innerHTML += inner;
                document.getElementById("inp").removeEventListener("change", FILE_URL.READ_FILE, false);
                document.getElementById("inp").addEventListener("change", FILE_URL.READ_FILE, false);
            });
            fr.readAsDataURL(this.files[0]);
        }
    }
}


