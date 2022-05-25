export default class FileViewer {

    static Show = (id, aes, fileUrlClass) => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {ACTION:"CONTENT", ID:id, MIME:true},
            success: response => {
                var name = response.name.replaceAll("_", "/")
                const fname = aes.decrypt(name, true)
                const b64 = aes.decrypt(response.ctx, true)
                const mime = aes.decrypt(response.mime, true)
                const ext = fname.includes('.') ? fname.split(".")[fname.split(".").length-1] : "file"
                FileViewer.Open(fname, ext, b64, mime, fileUrlClass)                
            },
            error: xhr => {
                console.log(xhr)
            }
        })
    }

    static Open = (fname, ext, b64, mime, fileUrlClass) => {

        ext = ext.toLowerCase()
        switch (ext) 
        {
            case 'pdf': 
            {
                FileViewer.PDF(b64, fname)
                break     
            }

            case 'mp4':
            {
                FileViewer.VID(b64, fname, fileUrlClass)
                break;
            } 
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
            case 'svg':
            case 'webp':
            {
                FileViewer.IMG(b64, fname, mime)
                break;
            }

            default:
            {
                FileViewer.TXT(b64, fname, fileUrlClass)
                break
            }
        }
    }

    static IMG = (b64, filename) => {

        var img = '<img width="auto" height="80%" src="'+b64+'" alt="'+filename+'">'
        var win = window.open("#","_blank");
        var title = filename;
        win.document.write('<html><title>'+ title +'</title><body style="margin-top:0px; margin-left: 0px; margin-right: 0px; margin-bottom: 0px;">');
        win.document.write(img);
        win.document.write('</body></html>');
        jQuery(win.document);
    }       

    static VID = (b64, filename, fileUrlClass) => {
        var BLOB = fileUrlClass.B64_2_BLOB(b64, filename);
        var BLOB_URL =  fileUrlClass.GET_BLOB_URL(BLOB);
        var vid = '<video width="80%" height="auto" controls src="'+BLOB_URL+'"/>';
        var win = window.open("#","_blank");
        var title = filename;
        win.document.write('<html><title>'+ title +'</title><body style="margin-top:0px; margin-left: 0px; margin-right: 0px; margin-bottom: 0px;">');
        win.document.write(vid);
        win.document.write('</body></html>');
        jQuery(win.document);
    }

    static PDF = (b64, filename) => {
        var objbuilder = '<object width="100%" height="100%" type="application/pdf" data="'+b64+'"></object>'
        var win = window.open("#","_blank");
        var title = filename;
        win.document.write('<html><title>'+ title +'</title><body style="margin-top:0px; margin-left: 0px; margin-right: 0px; margin-bottom: 0px;">');
        win.document.write(objbuilder);
        win.document.write('</body></html>');
        jQuery(win.document);
    }

    static TXT = async (b64, filename, fileUrlClass) => {
        var BLOB = await fileUrlClass.B64_2_BLOB(b64, filename).text();
        var txt = '<br><div style="padding:14px;border:2px solid black;margin-left:auto;margin-right:auto;width:80%;border-radius:10px"><textarea style="word-break:break-word;">'+BLOB+'</textarea></div><br>';
        var win = window.open("#","_blank");
        var title = filename;
        win.document.write('<html><title>'+ title +'</title><body style="background-color:rgb(230,230,230)">');
        win.document.write(txt);
        win.document.write('</body></html>');
        jQuery(win.document);
    }
}