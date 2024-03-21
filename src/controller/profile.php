<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../controller/user_keys.php';
    require_once __DIR__ . '/../../utils/securekit/crypto.php';
    require_once __DIR__ . '/../../utils/securekit/my_tfa.php';
    
    class ProfileController
    {
        public static function renderProfilePage()
        {
            $navbar = Navbar::getPrivate('profile');
                        
            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_by_userID();

            $recoverykey = UserKeysController::getRecoveryKey();
            $secret_2FA = UserKeysController::getSecret2FA($user->getUserID(), $recoverykey);

            $tfa = new MyTFA($user->getEmail(), $secret_2FA);
            $qrcode_url = $tfa->getQrcodeURL();

            include __DIR__ . '/../view/profile.php';
        }
    }

?>