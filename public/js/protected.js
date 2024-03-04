const checkSessionStatus = () => {

    $.ajax({
        
        url: '/sessions/current/status', 
        method: 'GET',
        success: function(response) {},
        error: function(xhr, status, error) {
            
            if (xhr.status !== undefined && parseInt(xhr.status) === 401)
            {
                window.location.href = "/signout";
                return;
            }

            console.log(xhr.responseText);
        }
    });
}