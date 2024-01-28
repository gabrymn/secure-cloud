export default class FileHandler 
{
    constructor(file_blob, file_ctx)
    {
        // in case we have a file name without extension
        if (!file_blob.name.includes('.'))
        {
            this.mime = "file"
            this.extension = "file"
        }
        else
        {
            const n = file_blob.name.split('.')
            this.extension = n[n.length-1]
            this.mime = file_blob.type === '' ? "file/"+n[n.length-1] : file_blob.type
        }
        
        this.name = file_blob.name
        this.size = {value: file_blob.size, unit: "byte"}
        this.ctx = FileHandler.#format_base_64(file_ctx)
        this.blob = file_blob
    }

    // this method returns the base64 (WITH URL, data:*/*;base64) of the file ctx
    // MORE INFO HERE: https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsDataURL
    static toBase64 = (file_blob) => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file_blob);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    })
    
    // In order to get the correct BASE_64 encode of the file context
    // we need to remove the first part of the string, that is: data:*/*;base64 
    // MORE INFO HERE: https://developer.mozilla.org/en-US/docs/Web/API/FileReader/readAsDataURL
    static #format_base_64 = base64_file_ctx_with_URL => {
        const regex = /^data:.+\/.+;base64,/;
        const base64_file_ctx_without_URL = base64_file_ctx_with_URL.replace(regex, '');
        return base64_file_ctx_without_URL;
    }

    static bytesOf(file_ctx) 
    {
        return encodeURI(file_ctx).split(/%..|./).length - 1;
    }

    static formatName = (unformat) => unformat.replaceAll("/", "_")
    static unformatName = (format) => format.replaceAll("_", "/")

    get_encrypted_as = (aes_obj, hash_func, return_type) => {

        const name = FileHandler.formatName(aes_obj.encrypt(this.name, true))
        
        const ctx = aes_obj.encrypt(this.ctx, true)
        
        const ctx_hashed = hash_func(name + ctx)

        const mime = aes_obj.encrypt(this.mime, true)

        switch (return_type.toLowerCase())
        {
            case 'array':
                return [name, this.extension, ctx, ctx_hashed, this.size.value, this.size.unit, mime]
            
            case 'json':
            default:
                return {
                    name:           name, 
                    extension:      this.extension, 
                    ctx:            ctx, 
                    ctx_hashed:     ctx_hashed, 
                    size_value:     this.size.value, 
                    size_unit:      this.size.unit, 
                    mime:           mime
                }
        }
    }
}
