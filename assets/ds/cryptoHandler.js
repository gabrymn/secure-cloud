
class CryptoHandler {
    
    static async generateRandomKey() 
    {
        const key = await crypto.subtle.generateKey(
            { 
                name: "AES-GCM", 
                length: 256 
            },
            true,
            ["encrypt", "decrypt"]
        );

        // Esporta la chiave come ArrayBuffer
        const keyBuffer = await crypto.subtle.exportKey("raw", key);

        // Converti l'ArrayBuffer in un array di byte
        const keyArray = Array.from(new Uint8Array(keyBuffer));

        // Converti l'array di byte in una stringa Base64
        const base64Key = btoa(String.fromCharCode.apply(null, keyArray));

        return base64Key;
    }

    static async encrypt(plainText, key) 
    {
        const encoder = new TextEncoder();
        const data = encoder.encode(plainText);
      
        // Importa la chiave dal formato Base64
        const keyBuffer = new Uint8Array(atob(key).split('').map(char => char.charCodeAt(0)));
        const aesKey = await crypto.subtle.importKey(
            "raw",
            keyBuffer,
            { name: "AES-GCM" },
            false,
            ["encrypt"]
        );
      
        // Genera un IV (Initialization Vector) casuale
        const iv = crypto.getRandomValues(new Uint8Array(12));
      
        // Cifra i dati
        const encryptedData = await crypto.subtle.encrypt(
            { 
                name: "AES-GCM", 
                iv: iv 
            },
            aesKey,
            data
        );
      
        // Restituisci il risultato come oggetto contenente il testo cifrato e l'IV
        return {
            cipherText: btoa(String.fromCharCode.apply(null, new Uint8Array(encryptedData))),
            iv: btoa(String.fromCharCode.apply(null, iv))
        };
    }

    static async decrypt(cipherText, key, iv) 
    {
        const decoder = new TextDecoder();
        const cipherData = new Uint8Array(atob(cipherText).split('').map(char => char.charCodeAt(0)));
        const ivData = new Uint8Array(atob(iv).split('').map(char => char.charCodeAt(0)));
      
        // Importa la chiave dal formato Base64
        const keyBuffer = new Uint8Array(atob(key).split('').map(char => char.charCodeAt(0)));
        const aesKey = await crypto.subtle.importKey
        (
            "raw",
            keyBuffer,
            { 
                name: "AES-GCM" 
            },
            false,
            ["decrypt"]
        );
      
        // Decifra i dati
        const decryptedData = await crypto.subtle.decrypt
        (
            { 
                name: "AES-GCM", 
                iv: ivData 
            },
            aesKey,
            cipherData
        );
      
        // Restituisci il testo decifrato
        return decoder.decode(decryptedData);
    }

    static async generateKeyFromPasswordAndSalt(password, salt) 
    {
        const encoder = new TextEncoder();
        const passwordBuffer = encoder.encode(password);
        const saltBuffer = salt instanceof Uint8Array ? salt : encoder.encode(salt);
      
        const keyMaterial = await crypto.subtle.importKey 
        (
            "raw",
            passwordBuffer,
            { 
                name: "PBKDF2" 
            },
            false,
            ["deriveBits", "deriveKey"]
        );
      
        const derivedKey = await crypto.subtle.deriveKey 
        (
            {
                name: "PBKDF2",
                salt: saltBuffer,
                iterations: 100000, 
                hash: "SHA-256"
            },
            keyMaterial,
            { 
                name: "AES-GCM", 
                length: 256 
            } 
        );
      
        // Esporta la chiave come ArrayBuffer
        const keyBuffer = await crypto.subtle.exportKey("raw", derivedKey);
      
        // Converti l'ArrayBuffer in un array di byte
        const keyArray = Array.from(new Uint8Array(keyBuffer));
      
        // Converti l'array di byte in una stringa Base64
        const base64Key = btoa(String.fromCharCode.apply(null, keyArray));
      
        return base64Key;
    }

    static async hash(text, algorithm = "SHA-256") {
        const encoder = new TextEncoder();
        const data = encoder.encode(text);
    
        const hashBuffer = await crypto.subtle.digest(algorithm, data);
    
        // Converte l'ArrayBuffer risultante in una stringa esadecimale
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashString = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');
    
        return hashString;
    }
}

export default CryptoHandler;