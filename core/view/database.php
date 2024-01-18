<?php
    
    #include_once '../class/db_req.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $server_name = "mariadb_container"; // nome del container
        $username = "root"; // Il tuo nome utente MySQL
        $password = "password"; // La tua password MySQL
        $dbname = "secure_cloud_db"; // Il nome del database a cui connettersi
        $table = "users";
        
        $file_array = [$_POST['name'], $_POST['extension'], $_POST['ctx'], $_POST['ctx_hashed'], $_POST['size_value'], $_POST['size_unit'], $_POST['mime']];

        try {
            $conn = new PDO("mysql:host=$server_name.;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $qry = "INSERT INTO $table (`name`,`extension`, `ctx`, `ctx_hashed`, `size_value`, `size_unit`, `mime`) VALUES ('$file_array[0]', '$file_array[1]', '$file_array[2]', '$file_array[3]', '$file_array[4]', '$file_array[5]', '$file_array[6]')";
            
            $prep = $conn->prepare($qry);
            $prep->execute();
            
            //$stmt = $conn->query($qry);
            //$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            http_response_code(200);
            echo json_encode(200);
            exit;

        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["error", "CONNECTION FAILED: " . $e->getMessage()]);
            exit;

        }
    }   


?>