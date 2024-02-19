const getPublicNavbar = (page=null) => 
{
    const style = "style='font-weight:900; color:white'";

    let home="style='color:white'", signin="style='color:white'", signup="style='color:white'";
    
    switch (page)
    {
        case 'home':
        {
            home = style;
            break;
        }
        case 'signup':
        {
            signup = style;
            break;
        }
        case 'signin':
        {
            signin = style;
            break;
        }
    }

    return (`
        <div class="container-fluid">
            <a class="navbar-brand" ${home} href="/">Home</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" ${signin} href="/signin">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" ${signup} href="/signup">Sign up</a>
                    </li>
                </ul>
            </div>
        </div>`
    );
}