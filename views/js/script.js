$( document ).ready(function() {

    var cookieValue = getCookie("modalShow_" + categ);

    if (!cookieValue || new Date() > new Date(cookieValue)) {
        $('#exampleModalCenter').modal('show');

        var expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + (24 * 60 * 60 * 1000));

        document.cookie = "modalShow_" + categ + "=true; expires=" + expirationDate.toUTCString() + "; path=/";
    }
});

function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
}