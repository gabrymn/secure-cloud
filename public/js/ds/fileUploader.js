export default class FileUploader {

    files;
    uploadSessionID;
    chunkSize;

    constructor(files) 
    {
        this.files = files;
        this.uploadSessionID = "";
        this.chunkSize = -1;
    }

    async initUploadSession() 
    {
        let formData = new FormData();

        formData.append('upload_space_required', this.getFilesSize());

        try {

            const response = await $.ajax({

                url: '/clouddrive/upload/initialize',
                data: formData,
                type: 'POST',
                processData: false,
                contentType: false,
            });
            
            console.log(response);

            if (response.upload_session_id && response.chunk_size) 
            {
                this.setUploadSessionID(response.upload_session_id);
                this.setChunkSize(response.chunk_size);
                return true;
            
            } else {

                const errorMsg = "Unexpected response context, try again later";
                alert(errorMsg);
                return false;
            }
            
        } 
        catch (error) 
        {
            const response = error.responseJSON;
            let errorMsg = "Unexpected error, try again later";

            if (response.status_message)
                errorMsg = response.status_message;
        
            alert(errorMsg);
            return false;
        }
    }

    async upload()
    {
        for (const file of this.files) 
        {
            const chunks = this.calculateChunks(file);
            this.uploadChunks(file.name, file.type, chunks);
        }
    }

    calculateChunks(file)
    {
        let start = 0;
        let chunks = [];

        while (start < file.size) 
        {
            let chunk = file.slice(start, start + this.chunkSize);
            chunks.push(chunk);
            start += this.chunkSize;
        }

        return chunks;
    }

    uploadChunks(filename, mimetype, chunks)
    {
        chunks.forEach((chunk, index) => {

            let formData = new FormData();
    
            formData.append('file', chunk);
            formData.append('upload_session_id', this.getUploadSessionID());
            formData.append('filename', filename);
            formData.append('mimetype', mimetype);
            formData.append('chunk_index', index);
            formData.append('chunks_len', chunks.length);

            $.ajax({
            
                url: '/clouddrive/upload',
                data: formData,
                type: "POST",
                processData: false,  
                contentType: false,  
                
                success: (response) => {

                    console.log(response);

                    if (response.fileid !== undefined && response.filename !== undefined)
                    {
                        this.addFileIcon(response.fileid, response.filename);
                        localStorage.setItem(response.fileid, response.filename);
                        
                        $('#' + response.fileid).on('click', () => {
                            
                            downloadFile(response.fileid);
                        });
                    }
                },
                error: (xhr) => {
                    alert(xhr.responseJSON.status_message);
                }
            });
        })

        return true;
    }

    addFileIcon(fileid, filename) 
    {
        $('#fileicons').append(
            `<tr id=${fileid}>
                <td><img src=img/fileicon.svg alt=Immagine></td>
                <td>${filename}</td>
            </tr>`
        );
    }

    setFiles(files)
    {
        this.files = files;
    }

    getFiles()
    {
        return this.files;
    }

    setUploadSessionID(sessionID) 
    {
        this.uploadSessionID = sessionID;
    }

    getUploadSessionID()
    {
        return this.uploadSessionID;
    }

    setChunkSize(chunkSize)
    {
        this.chunkSize = chunkSize;
    }

    getChunkSize()
    {
        return this.chunkSize;
    }

    getFilesSize() 
    {
        let sum = this.files.reduce((totalSize, file) => totalSize + file.size, 0);
        return sum;
    }
}