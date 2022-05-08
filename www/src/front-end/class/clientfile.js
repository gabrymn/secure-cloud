
export default class CLIENT_FILE {

    constructor(fileinf, filectx){
        this.name = fileinf.name
        this.size = {value: fileinf.size, unit: "bytes"}
        this.ctx = filectx
        this.all = fileinf
    }

    static TO_BASE64 = file => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });

    static BYTES_OF(CTX) {
        return encodeURI(CTX).split(/%..|./).length - 1;
    }

    static FORMAT_NAM = (NAM) => NAM.replaceAll("/", "_")
    static UNFORMAT_NAM = (NAM) => NAM.replaceAll("_", "/")

    ENCRYPT = (aes, hash) => {
        const NAM = CLIENT_FILE.FORMAT_NAM(aes.encrypt(this.name, true))
        const CTX = aes.encrypt(this.ctx, true)
        const IMP = hash(NAM + CTX)
        const SIZ = CLIENT_FILE.BYTES_OF(CTX)
        return [NAM,CTX,IMP,SIZ]
    }
}