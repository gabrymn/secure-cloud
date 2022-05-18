
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
        var plaintext = wordArray.toString(CryptoJS.enc.Utf8);
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