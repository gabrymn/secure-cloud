<?php

    class Navbar
    {
        private const string DEFAULT_STYLE = "style='color:white'";
        private const string FOCUS_STYLE = "style='font-weight:900; color:white'";

        // navbar used in protected pages
        public static function getPrivate(?string $focus_page = null) : string
        {
            $clouddrive = self::DEFAULT_STYLE;
            $storage = self::DEFAULT_STYLE;
            $transfers = self::DEFAULT_STYLE;
            $sessions = self::DEFAULT_STYLE;
            $profile = self::DEFAULT_STYLE;

            switch ($focus_page)
            {
                case 'clouddrive':
                {
                    $clouddrive = self::FOCUS_STYLE;
                    break;
                }
                case 'storage':
                {
                    $storage = self::FOCUS_STYLE;
                    break;

                }
                case 'transfers':
                {
                    $transfers = self::FOCUS_STYLE;
                    break;
                }

                case 'sessions':
                {
                    $sessions = self::FOCUS_STYLE;
                    break;
                }

                case 'profile':
                {
                    $profile = self::FOCUS_STYLE;
                    break;
                }
            }

            return ('
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:3px solid #157EFB">
                    <div class="container-fluid">
                        <a class="navbar-brand" ' . $clouddrive . ' href="/clouddrive">Cloud Drive</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link active" ' . $storage . ' aria-current="page" href="/storage">Storage</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" ' . $transfers . ' aria-current="page" href="/transfers">Transfers</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" ' . $sessions . ' aria-current="page" href="/sessions">Sessions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" ' . $profile . ' aria-current="page" href="/profile">Profile</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            ');
        }

        // navbar used in public pages like signin/signup/recover...
        public static function getPublic(?string $focus_page = null) : string
        {
            $home = self::DEFAULT_STYLE;
            $signin = self::DEFAULT_STYLE;
            $signup = self::DEFAULT_STYLE;

            switch ($focus_page)
            {
                case 'signin':
                {
                    $signin = self::FOCUS_STYLE;
                    break;
                }
                case 'signup':
                {
                    $signup = self::FOCUS_STYLE;
                    break;

                }
                case 'home':
                {
                    $home = self::FOCUS_STYLE;
                    break;
                }
            }

            return ('
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:3px solid #157EFB">
                    <div class="container-fluid">
                        <a class="navbar-brand" ' . $home . '  href="/">Home</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" ' . $signin . ' href="/signin">Sign in</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" ' . $signup . ' href="/signup">Sign up</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            ');
        }
    }

     

?>