
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

    encrypt = (cleartext) => {
        const wordArray = CryptoJS.AES.encrypt(cleartext, this.key);
        const ciphertext = wordArray.toString();   
        return ciphertext;
    }        
    
    decrypt = (ciphertext) => {
        const wordArray = CryptoJS.AES.decrypt(ciphertext, this.key);
        const cleartext = wordArray.toString(CryptoJS.enc.Utf8);
        return cleartext;
    }
}