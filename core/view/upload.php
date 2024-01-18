<?php


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <input type="file" id="ID_FILE_UPLOADER" multiple/>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
</body>
</html>

<script type="module">    

    import FileHandler from '/res/fileHandler.js';
    import cryptolib from '/res/cryptolib.js';
    import request from '/res/request.js';

    async function success(result){
        console.log("Success:", result);
    }

    async function failure(error){
        console.error("Error:", error);
    }

    $("#ID_FILE_UPLOADER").on('change', async (e) => {

        var files_blob = Object.values(e.target.files)

        files_blob.forEach((file_blob) => {

            // given the blob of the file, this method'll return the context of the file
            FileHandler.toBase64(file_blob) 
                .then(async (file_ctx) => {

                    const x = new FileHandler(file_blob, file_ctx);
                    
                    const hash_func = cryptolib['HASH'].sha256;

                    var aes_obj = new cryptolib['AES']("THIS_IS_A_PRIVATE_KEY_X823I8YOHCBNX293DHG32Y2UWDH2U3IDN2")

                    const encrypted_data = x.get_encrypted_as(aes_obj, hash_func, "json");
                    
                    // request example:

                    const formData = new FormData();

                    formData.append("name", encrypted_data.name)
                    formData.append("extension", encrypted_data.extension)
                    formData.append("ctx", encrypted_data.ctx)
                    formData.append("ctx_hashed", encrypted_data.ctx_hashed)
                    formData.append("size_value", encrypted_data.size_value)
                    formData.append("size_unit", encrypted_data.size_unit)
                    formData.append("mime", encrypted_data.mime)

                    const URL = "https://80-gabrielemig-clientserve-r7onoeqrkmw.ws-eu107.gitpod.io/api/database.php";
                    
                    await request(URL, 'POST', formData, success, failure);

                }).catch((error) => alert("Errore nel caricamento del file, riprova"));

            /*
            if (!checkStorage(Number(file.size)))
            {
                alert("Spazio esaurito, elimina dei contenuti e riprova")
                return
            }
            CLIENT_FILE.TO_BASE64(file)
                .then((ctx) => {

                    var name = handleExists(file.name)
                    const fobj = new CLIENT_FILE(file,ctx,name)

                    const hash = cryptolib['HASH'].SHA256;
                    const [NAM, CTX, IMP, SIZ, MME] = fobj.ENCRYPT(aes, hash);
                    $.ajax({
                        type: 'POST',
                        url: "../../back-end/class/client_resource_handler.php",
                        data: {NAM: NAM, CTX: CTX, IMP: IMP, SIZ: SIZ, MME: MME},
                        success: (response) => {
                            console.log(response)
                            location.reload()
                        },
                        error: (xhr) => {
                            console.log(xhr);
                        }
                    });
                })
                .catch((error) => alert("Errore nel caricamento del file, riprova"))*/
        })
    });


</script>