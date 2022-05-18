export default function getIcon (extension) {

    switch (extension.toLowerCase())
    {
        case 'png':
        case 'jpeg':
        case 'jpg':
        case 'svg':
        case 'webp':
            return 	'fa fa-file-image-o'
        case 'mp3':
            return 'fa fa-file-audio-o'
        case 'mp4':
            return 'fa fa-file-movie-o'
        case 'c':
        case 'cpp':
        case 'sql':
        case 'py':
        case 'cs':
        case 'java':
        case 'json':
        case 'xml':
            return 'fa fa-file-code-o' 
        case 'txt':
        case 'rft':
            return 'fa fa-file-text-o'
        case 'pdf':
            return 'fa fa-file-pdf-o'
        case 'dot':
        case 'odt':
        case 'docx':
        case 'dotm':
            return 'fa fa-file-word-o'
        case 'xlsx':
        case 'csv':
        case 'ods':
            return 'fa fa-file-excel-o'
        case 'zip':
        case 'rar':
            return 'fa fa-file-zip-o'
        default: 
            return 'fa fa-file-o'
    }
} 