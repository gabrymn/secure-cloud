<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card">
                    <div class="card-header bg-dark text-white">Sign up</div>
                    <div class="card-body">

                        <form id="signup_form">
                            <div class="form-group row">
                                <label for="id_name" class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-8">
                                    <input name="name" type="text" id="id_name" class="form-control" minlength="2" maxlength="30" placeholder="John" oninput="capitalizeFirstLetter('id_name')" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_surname" class="col-md-4 col-form-label text-md-right">Surname</label>
                                <div class="col-md-8">
                                    <input name="surname" type="text" id="id_surname" class="form-control" minlength="2" maxlength="30" placeholder="Doe" oninput="capitalizeFirstLetter('id_surname')" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="id_email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-8">
                                    <input name="email" type="email" id="id_email" class="form-control" placeholder="user@example.com" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pwd" class="col-md-4 col-form-label text-md-right">New password</label>
                                <div class="col-md-8">
                                    <input name="pwd" type="password" id="pwd" class="form-control"  placeholder="••••••" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pwd_confirm" class="col-md-4 col-form-label text-md-right">Confirm password</label>
                                <div class="col-md-8">
                                    <input name="pwd_confirm" type="password" id="pwd_confirm" class="form-control"  placeholder="••••••" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="js/pages/signup.js"></script>
    <script src="js/url.js"></script>
</body>
</html>
