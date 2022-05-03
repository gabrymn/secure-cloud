
/*
    <body>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    </body>

    <script type="module">
        import cryptolib from "crypto.js";
        const aes = new cryptolib['AES']("mykey");
        console.log(aes.decrypt(aes.encrypt("plaintext")));

    </script>

    const AES = cryptolib['AES'];

*/

var cryptolib = [];

cryptolib['AES'] = class AES {

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

cryptolib['HASH'] = class HASH {

    static SHA256 = (data, compression = false) => {
        if (compression) data = btoa(data); 
        const hashed = CryptoJS.SHA256(data).toString(CryptoJS.enc.Hex);
        return hashed;
    }
}

export default cryptolib;