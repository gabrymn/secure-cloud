
export default class CLIENT_FILE {

    constructor(fileinf, filectx, filename){
        if (!filename.includes('.'))
        {
            this.mime = "file"
            this.ext = "file"
        }
        else
        {
            const n = filename.split('.')
            this.ext = n[n.length-1]
            this.mime = fileinf.type === '' ? 
                "file/"+n[n.length-1] : fileinf.type
        }
        this.name = filename
        this.size = {value: fileinf.size, unit: "byte"}
        this.ctx = filectx
        this.all = fileinf
    }

    static TO_BASE64 = file => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    })

    static BYTES_OF(CTX) {
        return encodeURI(CTX).split(/%..|./).length - 1;
    }

    static FORMAT_NAM = (NAM) => NAM.replaceAll("/", "_")
    static UNFORMAT_NAM = (NAM) => NAM.replaceAll("_", "/")

    ENCRYPT = (aes, hash) => {
        const NAM = CLIENT_FILE.FORMAT_NAM(aes.encrypt(this.name, true))
        const CTX = aes.encrypt(this.ctx, true)
        const IMP = hash(NAM + CTX)
        const SIZ = this.size.value
        const MME = aes.encrypt(this.mime, true)
        return [NAM,CTX,IMP,SIZ,MME]
    }
}