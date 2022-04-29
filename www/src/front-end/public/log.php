<?php   

    require_once '../resources/api.php';
    require_once '../resources/OAuth/google/vendor/autoload.php';

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {

                if (isset($_REQUEST['EMAIL']) && isset($_REQUEST['PASS'])){

                    if (filter_var($_REQUEST['EMAIL'], FILTER_VALIDATE_EMAIL)){

                        sqlc::connect();
                        if (sqlc::login($_REQUEST['EMAIL'], $_REQUEST['PASS'])){
                            
                            $id_user = sqlc::get_id_user($_REQUEST['EMAIL']);
                            
                            if (isset($_REQUEST['REM_ME']) && $_REQUEST['REM_ME']){
                                system::remember($id_user); 
                                unset($_REQUEST['REM_ME']);
                            }
                            unset($_REQUEST['EMAIL']);
                            unset($_REQUEST['PASS']);

                            system::redirect_priv_area($id_user);
                            exit;

                        }else { 
                            response::ssend(400, $error, "Incorrect credentials"); 
                        }

                    }else{ 
                        response::ssend(400, $error, "Incorrect email"); 
                    }

                }else { 
                    response::client_error(404); 
                }
            }

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID'])){
                    session_start();
                    if (isset($_SESSION['ID_USER'])){
                        system::redirect_priv_area($_SESSION['ID_USER']);
                    }
                }

                if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
                    if ($_COOKIE['logged']){
                        $tkn = $_COOKIE['rm_tkn'];
                        $htkn = hash("sha256", $tkn);
                        sqlc::connect();
                        $data = sqlc::rem_sel($htkn);
                        if ($data) system::redirect_priv_area($data['id_user']);
                    }
                }
                
                break;
            }

            default: {

                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);

?>

<html lang="en">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<div class="wrapper fadeInDown">
  <div id="formContent">
    <div class="fadeIn first">
        <br><br>
    </div>

    <form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">
        <input type="text" id="login" class="fadeIn second" name="EMAIL" placeholder="EMAIL" required>
        <input type="password" id="password" class="fadeIn third" name="PASS" placeholder="PASSWORD" required><br>
        <input name="REM_ME" type="checkbox" value="1" id="REM_ME"> <label for="REM_ME">Remember me</label>
        <input type="submit" class="fadeIn fourth" value="Login">
        <p style="color:red"><?php echo $error; ?></p>
        <span>Not registered? <a href="signup">Signup</a></span>
        <br>
        <span>Forgot password? <a href="password_reset">Click here</a></span>
        <br><br><br>
        <div class="row" style="display:block;margin-left:auto;margin-right:auto;width:40%;">
            <div class="col-md-3">
                <a class="btn btn-outline" href="<?php echo $client->createAuthUrl(); ?>" role="button" style="text-transform:none">
                    <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                    Sign in with Google
                </a>
            </div>
        </div>
    </form>

  </div>
</div>

<script src="https://www.mywebs.altervista.org/Final/resources/api.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" 
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" 
        crossorigin="anonymous">
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

    $('document').ready(() => {
        if (getCookie('ALLOW') === ''){
            let text = 'Accetti i cookie?';
            if (confirm(text) === true) 
                setCookie('ALLOW', '1');
        }
    });

    if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);
    //$('#REM_ME').prop('checked', 1);

    $('#REM_ME').on('change', () => {
        const checked = $('#REM_ME').prop('checked') + "";
        $('#REM_ME').val(checked);
    });

</script>

<style>
html {
  background-color: #56baed;
}

body {
  font-family: "Poppins", sans-serif;
  height: 100vh;
}

a {
  color: #92badd;
  display:inline-block;
  text-decoration: none;
  font-weight: 400;
}

h2 {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  display:inline-block;
  margin: 40px 8px 10px 8px; 
  color: #cccccc;
}

.wrapper {
  display: flex;
  align-items: center;
  flex-direction: column; 
  justify-content: center;
  width: 100%;
  min-height: 100%;
  padding: 20px;
}

#formContent {
  -webkit-border-radius: 10px 10px 10px 10px;
  border-radius: 10px 10px 10px 10px;
  background: #fff;
  padding: 30px;
  width: 90%;
  max-width: 450px;
  position: relative;
  padding: 0px;
  -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
  box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
  text-align: center;
}

#formFooter {
  background-color: #f6f6f6;
  border-top: 1px solid #dce8f1;
  padding: 25px;
  text-align: center;
  -webkit-border-radius: 0 0 10px 10px;
  border-radius: 0 0 10px 10px;
}


h2.inactive {
  color: #cccccc;
}

h2.active {
  color: #0d0d0d;
  border-bottom: 2px solid #5fbae9;
}


input[type=button], input[type=submit], input[type=reset]  {
  background-color: #56baed;
  border: none;
  color: white;
  padding: 15px 80px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  text-transform: uppercase;
  font-size: 13px;
  -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
  margin: 5px 20px 40px 20px;
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -ms-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s ease-in-out;
  transition: all 0.3s ease-in-out;
}

input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover  {
  background-color: #39ace7;
}

input[type=button]:active, input[type=submit]:active, input[type=reset]:active  {
  -moz-transform: scale(0.95);
  -webkit-transform: scale(0.95);
  -o-transform: scale(0.95);
  -ms-transform: scale(0.95);
  transform: scale(0.95);
}

input[type=text], input[type="password"] {
  background-color: #f6f6f6;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 5px;
  width: 85%;
  border: 2px solid #f6f6f6;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
}

input[type=text]:focus, input[type="password"]:focus {
  background-color: #fff;
  border-bottom: 2px solid #5fbae9;
}

input[type=text]:placeholder, input[type="password"]:placeholder {
  color: #cccccc;
}

.fadeInDown {
  -webkit-animation-name: fadeInDown;
  animation-name: fadeInDown;
  -webkit-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}

@-webkit-keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

@-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.fadeIn {
  opacity:0;
  -webkit-animation:fadeIn ease-in 1;
  -moz-animation:fadeIn ease-in 1;
  animation:fadeIn ease-in 1;

  -webkit-animation-fill-mode:forwards;
  -moz-animation-fill-mode:forwards;
  animation-fill-mode:forwards;

  -webkit-animation-duration:1s;
  -moz-animation-duration:1s;
  animation-duration:1s;
}

.fadeIn.first {
  -webkit-animation-delay: 0.4s;
  -moz-animation-delay: 0.4s;
  animation-delay: 0.4s;
}

.fadeIn.second {
  -webkit-animation-delay: 0.6s;
  -moz-animation-delay: 0.6s;
  animation-delay: 0.6s;
}

.fadeIn.third {
  -webkit-animation-delay: 0.8s;
  -moz-animation-delay: 0.8s;
  animation-delay: 0.8s;
}

.fadeIn.fourth {
  -webkit-animation-delay: 1s;
  -moz-animation-delay: 1s;
  animation-delay: 1s;
}

.underlineHover:after {
  display: block;
  left: 0;
  bottom: -10px;
  width: 0;
  height: 2px;
  background-color: #56baed;
  content: "";
  transition: width 0.2s;
}

.underlineHover:hover {
  color: #0d0d0d;
}

.underlineHover:hover:after{
  width: 100%;
}


*:focus {
    outline: none;
} 
</style>
</html>