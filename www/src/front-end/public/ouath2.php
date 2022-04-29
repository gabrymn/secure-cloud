 // Dopo che l'utente clicca => sign in with google
                if (isset($_GET['code']))
                {
                    $client = client_google::get_object();
                    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                    
                    try {
                        $client->setAccessToken($token['access_token']);
                    } catch (Exception $e){
                        response::client_error(400, "Invalid URL");
                    };

                    $google_oauth = new Google_Service_Oauth2($client);
                    $google_account_info = $google_oauth->userinfo->get();
                    $email =  $google_account_info->email;
                    $name =  $google_account_info->name;
                    $data = array($email, $name);
                    
                    header("Location: pvt.php");
                    
                    sqlc::connect();
                    switch (sqlc::sel_OAuth2($email)) {
                        // Appena registrato con google OAuth
                        case 0: {
                            sqlc::ins_OAuth2($email);
                            session_start();
                            $id = sqlc::get_id_user($email);
                            $_SESSION['ID_USER'] = $id;
                            echo "<h1 style='color:blue'>You signed up with google successfully<br></h1><br/>";
                            echo "<h2><a href='private'>Continue</a></h2>";
                            exit;
                            break;
                        }
                        // Già registrato con google OAuth-> redirect area privata
                        case 1: {
                            $id = sqlc::get_id_user($email);
                            system::redirect_priv_area($id);
                            break;
                        }
                        // l'utente si è già registrato con questa email attraverso (email/password)
                        case -1: {
                            response::client_error(400, "email already taken");
                            break;
                        }
                        // Caso non previsto -> errore 500
                        default: {
                            response::server_error(500);
                            break;
                        }
                    }
                }
                else
                {	
                    // quando l'utente fa una r.get alla pagina di login
                    $client = client_google::get_object();
                }

                break;
            }