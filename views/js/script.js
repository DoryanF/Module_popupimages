$( document ).ready(function() {
    if (!localStorage.getItem("modalShow")) {
        $('#exampleModalCenter').modal('show');

        localStorage.setItem("modalShow", "true");

    }
});