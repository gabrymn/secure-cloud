
/*
    <body>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    </body>

    <script type="module">
        import AES from "crypto.js";
        const aes = new AES("mykey");
        console.log(aes.decrypt(aes.encrypt("plaintext")));
    </script>

*/

export default class AES {

    constructor(key) {
        this.key = key;
        this.bit = key.toString().length * 4;
    }

    encrypt = (plaintext, compression = false) => {
        if (compression) plaintext = btoa(plaintext);
        const wordArray = CryptoJS.AES.encrypt(plaintext, this.key);
        const ciphertext = wordArray.toString();
        return ciphertext;
    }

    decrypt = (ciphertext, decompression = false) => {
        const wordArray = CryptoJS.AES.decrypt(ciphertext, this.key);
        const plaintext = wordArray.toString(CryptoJS.enc.Utf8);
        if (decompression) plaintext = atob(plaintext);
        return plaintext;
    }
}
