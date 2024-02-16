
// remove question mark from URL
if (window.location.href.indexOf('?') > -1) 
{
    var newUrl = window.location.href.split('?')[0];
    window.history.replaceState({}, document.title, newUrl);
}
