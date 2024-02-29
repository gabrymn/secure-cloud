<?php

    class FileIcons
    {
        public static function get(array $files)
        {
            $fileicons = "";
            
            foreach ($files as $file)
            {
                $fileicons .= (
                    '<tr id='.$file['id_file'].' onclick="downloadFile('."'". $file['id_file'] ."'". ')">
                        <td><img src="img/fileicon.svg" alt="Immagine"></td>
                        <td>'.$file['fullpath'].'</td>
                    </tr>'
                );
            }

            return ('<table id="fileicons" border="1">
                <thead>
                    <tr>
                        <th>Icona</th>
                        <th>Nome File</th>
                    </tr>
                </thead>
                <tbody>
                    '.$fileicons.'
                </tbody>
            </table>');
        }
    }


?>