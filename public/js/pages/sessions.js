const expireSession = (id) => {
    
    id = String(id)
    const session_token = sessionRefs.get(id);

    if (session_token === CURRENT_SESSION_TOKEN)
    {
        const userResponse = confirm("You're about to expire the current session. Do you want to continue?")
        if (!userResponse) return
    }

    $.ajax({
        
        url: '/sessions/expire', 
        method: 'POST',
        data: {
            session_token: session_token,
        },
        success: function(response) {
            
            console.log(response);

            const id_status = 'id_sess_status_' + id;
            const id_btn = 'id_td_expire_' + id;

            $('#' + id_status).html("Expired");
            $('#' + id_btn).html(null);

            if (response.redirect !== undefined)
                window.location.href = response.redirect;
        },
        error: function(xhr, status, error) {
            console.error(xhr);
            alert('There was an error while tryin to expire the session, try again');
        }
    });
}


