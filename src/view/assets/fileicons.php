<?php

    class FileIcons
    {
        public static function get(array $filenames)
        {
            $fileicons = "";
            
            foreach ($filenames as $filename)
            {
                $fileicons .= (
                    '<tr>
                        <td><img src="img/fileicon.svg" alt="Immagine"></td>
                        <td>'.$filename.'</td>
                    </tr>'
                );
            }

            return ('<table border="1">
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