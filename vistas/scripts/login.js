$("#btnlogin").on('click', function (e) {
        e.preventDefault();
        logina = $("#logina").val();
        clavea = $("#clavea").val();

        $.post("../ajax/usuario.php?op=verificar",
                { "logina": logina, "clavea": clavea },
                function (data) {
                       
                        if (!data.includes("Usuario")) {
                                $(location).attr("href", "escritorio.php");
                        } else {

                                swal({
                                        title: "Error!",
                                        text: `${data}` ,
                                        icon: "error",
                                        button: "Ok"
                                });
                              
                        }
                });
})