
export default class CryptoHandler 
{
    static async genAESKey(bit) 
    {
        if (![128, 256].includes(bit)) 
            return false;
      
        try 
        {
            const key = await window.crypto.subtle.generateKey
            (
                {
                    name: 'AES-GCM',
                    length: bit
                },
                true,
                ['encrypt', 'decrypt']
            );
      
            const exportedKey = await window.crypto.subtle.exportKey('raw', key);
            const base64Key = btoa(String.fromCharCode.apply(null, new Uint8Array(exportedKey)));
            return base64Key;

        } 
        catch (error) 
        {
            return false;
        }
    }

    static async encrypt(data, key) 
    {
        const keyBuffer = new Uint8Array(atob(key).split('').map(char => char.charCodeAt(0)));
        const iv = crypto.getRandomValues(new Uint8Array(12)); // Inizializzazione vettore (IV) di 96 bit
        
        return crypto.subtle.importKey('raw', keyBuffer, { name: 'AES-GCM' }, false, ['encrypt'])
        .then((cryptoKey) => 
        {
            return crypto.subtle.encrypt({ name: 'AES-GCM', iv: iv }, cryptoKey, new TextEncoder().encode(data));
        })
        .then((encrypted) => 
        {
            const encryptedArray = new Uint8Array(encrypted);
            const result = { 
                ciphertext: btoa(String.fromCharCode.apply(null, encryptedArray)), 
                iv: btoa(String.fromCharCode.apply(null, iv)) 
            };
            return result;
        });
    }
  
    static async decrypt(ciphertext, key, iv) 
    {
        const keyBuffer = new Uint8Array(atob(key).split('').map(char => char.charCodeAt(0)));
        const ivBuffer = new Uint8Array(atob(iv).split('').map(char => char.charCodeAt(0)));
        const ciphertextBuffer = new Uint8Array(atob(ciphertext).split('').map(char => char.charCodeAt(0)));
        
        return crypto.subtle.importKey('raw', keyBuffer, { name: 'AES-GCM' }, false, ['decrypt'])
        .then((cryptoKey) => {
            return crypto.subtle.decrypt({ name: 'AES-GCM', iv: ivBuffer }, cryptoKey, ciphertextBuffer);
        })
        .then((decrypted) => {
            return new TextDecoder().decode(decrypted);
        });
    }

    static async deriveKeyFrom(password, bit, iterations = 10000, salt = 'password') 
    {
        // Codifica la password e il salt come ArrayBuffer
        const passwordBuffer = new TextEncoder().encode(password);
        const saltBuffer = new TextEncoder().encode(salt);
      
        // Utilizza PBKDF2 per derivare la chiave
        const derivedKeyBuffer = await window.crypto.subtle.importKey
        (
            'raw',
            passwordBuffer,
            { name: 'PBKDF2' },
            false,
            ['deriveKey']
        
        ).then(async (key) => {
            return window.crypto.subtle.deriveKey
            (
                {
                    name: 'PBKDF2',
                    salt: saltBuffer,
                    iterations: iterations,
                    hash: { name: 'SHA-256' }
                },
                key,
                { name: 'AES-GCM', length: bit },
                true,
                ['encrypt', 'decrypt']
            );
        });
      
        // Converti la chiave derivata in un array di byte
        const derivedKeyArray = new Uint8Array(await window.crypto.subtle.exportKey('raw', derivedKeyBuffer));
      
        // Converte la chiave derivata in base64
        const base64Key = btoa(String.fromCharCode.apply(null, derivedKeyArray));
        
        return base64Key;
    }
      
    static async hash(text, algo = "SHA-256") {

        const encoder = new TextEncoder();
        const data = encoder.encode(text);
    
        const hashBuffer = await crypto.subtle.digest(algo, data);
    
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashString = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');
    
        return hashString;
    }
}