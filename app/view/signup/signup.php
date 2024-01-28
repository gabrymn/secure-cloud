<!------ START BOOTSTRAP FORM ---------->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE CLOUD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
        <div class="container-fluid">
            <a class="navbar-brand" href="../">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" style="color:white" href="../signin">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="font-weight:900;color:white" href="signup.php">Sign up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div id="input_error" style="display:none" class="alert alert-danger" onclick="this.style.display='none'" role="alert"></div>
                    
                    <div class="card">
                        <div class="card-header">Sign up</div>
                        <div class="card-body">
                            <form id="signup_form">
                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                    <div class="col-md-6">
                                        <input name="name" type="text" id="id_name" class="form-control" minlength="2" maxlength="30" placeholder="John" oninput="capitalizeFirstLetter('id_name')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="surname" class="col-md-4 col-form-label text-md-right">Surname</label>
                                    <div class="col-md-6">
                                        <input name="surname" type="text" id="id_surname" class="form-control" minlength="2" maxlength="30" placeholder="Smith" oninput="capitalizeFirstLetter('id_surname')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="email" type="email" id="id_email" class="form-control" placeholder="user@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New password</label>
                                    <div class="col-md-6">
                                        <input name="pwd" type="password" id="pwd" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Confirm password</label>
                                    <div class="col-md-6">
                                        <input name="pwd_confirm" type="password" id="pwd_confirm" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <div id="ID_COOKIE_BOX" class="row" style="display:none">
        <div class="col-md-4 col-sm-12 button-fixed">
        <div class="p-3 pb-4 bg-custom text-white">
        <div class="row">
        <div class="col-10">
        <h1>Allow Cookies</h1>
        </div>
        <div class="col-2 text-center">
        <i class="fas fa-times"></i>
        </div>
        </div>
        <p>Utilizziamo i cookie per migliorare la tua esperienza</p>
        <button id="ID_COOKIE_A" type="button" class="btn btn-light w-100">Accept</button>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
</body>
</html>

<script type="module">

    import CryptoHandler from '../DS/cryptoHandler.js'

    $('#signup_form').on('submit', async (e) => {
        // blocca la richiesta HTTP della form
        e.preventDefault();

        if (validateInputs())
        {
            var formData = new FormData(document.getElementById('signup_form'));
            
            formData.delete('pwd_confirm');

            const keys = await genKeys();

            formData.append('rkey', keys['rkey']);
            formData.append('rkey_c', keys['rkey_c']);
            formData.append('ckey_c', keys['ckey_c']);
            formData.append('rkey_iv', keys['rkey_iv']);
            formData.append('ckey_iv', keys['ckey_iv']);

            const url = 'http://localhost/api/signup/main.php';
            const method = 'POST';

            try {
                const response = await fetch(url, 
                {
                    method: method,
                    body: formData,
                });

                if (response.ok)
                {
                    // test
                    //console.log(await response.text());
                    //return false;
                    
                    const json = await response.json();
                    window.location.href = json.redirect;
                }
                else
                {
                    const errorTxt = await response.text();
                    const errorJson = JSON.parse(errorTxt);
                    $('#input_error').css("display", "block");
                    $('#input_error').html(errorJson.status_message);
                }

            } catch (error) {
                console.log(error)
                $('#input_error').css("display", "block");
                $('#input_error').html("There was a problem, try again");
            }
        }

        e.preventDefault();
    });

    const genKeys = async () => {

        const rkey = await CryptoHandler.genAESKey(256)
        const ckey = await CryptoHandler.genAESKey(256)
        const dkey = await CryptoHandler.deriveKeyFrom($("#pwd").val(), 256)

        const rkey_obj = await CryptoHandler.encrypt(rkey, dkey)
        const ckey_obj = await CryptoHandler.encrypt(ckey, rkey)

        return {
            "rkey": rkey, 
            "rkey_c": rkey_obj.ciphertext, 
            "ckey_c": ckey_obj.ciphertext, 
            "rkey_iv": rkey_obj.iv,
            "ckey_iv": ckey_obj.iv
        };
    }

    const validateInputs = () => {
        if ($('#id_name').val().length < 2 || $('#id_surname').val().length < 2)
        {
            $('#input_error').css("display", "block");
            $('#input_error').html("Name and surname must have at least 2 characters");
            return false;
        }
        if ($('#pwd').val() !== $('#pwd_confirm').val())
        {
            $('#input_error').css("display", "block");
            $('#input_error').html("Passwords don't match");
            return false;
        }
        if ($('#pwd').val().length < 2)
        {
            $('#input_error').css("display", "block");
            $('#input_error').html("Password must have at least 8 characters");
            return false;
        }
        
        return true;
    }

</script>

<script>

    const capitalizeFirstLetter = (id) => {
        let inputElement = document.getElementById(id);
        let inputValue = inputElement.value;

        let formattedValue = inputValue.replace(/\b\w/g, function (match) {
            return match.toUpperCase();
        });

        inputElement.value = formattedValue;
    }

</script>