<?php

    require_once "../../back-end/class/sqlc.php";
    sqlc::init_db();

?>


<body>
    <input id="inp" type='file'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
</body>
<script type="module">
    import FILE_URL from "../class/img_url.js";
    import cryptolib from "../class/crypto.js";

    const FORMAT_EFILENAME = (efilename) => efilename.replaceAll("/", "_")

    document.getElementById("inp").addEventListener("change", FILE_URL.READ_FILE, false);

    var aes = new cryptolib['AES']("a3d56391ba408f31e791e9ea44ea97d340da186fe54309b882d5923da5ddfd9f");

    const e_filename = FORMAT_EFILENAME(aes.encrypt(sessionStorage.getItem("filename"), true));
    const e_filedata = aes.encrypt(sessionStorage.getItem("data"), true);

    const h = cryptolib['HASH'].SHA256(e_filename + e_filedata);

    console.log(e_filename);

    $.ajax({
        type: 'POST',
        url: "../../back-end/class/client_resource_handler.php",
        data: {NAME: e_filename, DATA: e_filedata, H: h},
        success: (response) => {
            console.log(response);
        },
        error: (xhr) => {
            console.log(xhr);
        }
    });


    console.log(aes);

</script>
