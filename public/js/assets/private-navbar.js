const getPrivateNavbar = (page = null) =>{

    const style = "style='font-weight:900; color:white'";
    
    let clouddrive="style='color:white'", transfers="style='color:white'", storage="style='color:white'", sessions="style='color:white'";
    
    switch (page)
    {
        case 'clouddrive':
        {
            clouddrive = style;
            break;
        }
        case 'storage':
        {
            storage = style;
            break;
        }
        case 'transfers':
        {
            transfers = style;
            break;
        }
        case 'sessions':
        {
            sessions = style;
            break;
        }
    }

    return (`
        <div class="container-fluid">
            <a class="navbar-brand" ${clouddrive} href="/clouddrive">Cloud Drive</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" ${storage} aria-current="page" href="/storage">Storage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" ${transfers} aria-current="page" href="/transfers">Transfers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" ${sessions} aria-current="page" href="/sessions">Sessions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/profile">Profile</a>
                    </li>
                </ul>
            </div>
        </div>`
    );
}