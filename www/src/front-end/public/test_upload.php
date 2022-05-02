<?php

require_once "../../back-end/class/sqlc.php";
sqlc::init_db();

?>


<body>
    <input id="inp" type='file'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
</body>
<script type="module">
    import FILE_URL from "../class/img_url.js";
    import AES from "../class/crypto.js";
    import EN_FILE from "../class/img_url.js";

    document.getElementById("inp").addEventListener("change", FILE_URL.READ_FILE, false);

    var aes = new AES("a3d56391ba408f31e791e9ea44ea97d340da186fe54309b882d5923da5ddfd9f");

    const e_filename = aes.encrypt(sessionStorage.getItem("filename"), true);
    const e_filedata = aes.encrypt(sessionStorage.getItem("data"), true);

    console.log(e_filename);
    console.log(e_filedata);

    const obj = {e_filename, e_filedata};

    $.ajax({
        type: 'POST',
        url: "../../back-end/class/client_resource_handler.php",
        data: {NAME: e_filename, DATA: e_filedata},
        success: (response) => {
            console.log(response);
        },
        error: (xhr) => {
            console.log(xhr);
        }
    });


    console.log(aes);

</script>
