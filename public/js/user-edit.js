$(document).ready(function () 
{   
    // Script permettant d'éditer la datatable en faisant un double clic sur une cellule ayant la classe editable (pas l'id)
    var table = $('#usersTable').DataTable();

    $('#usersTable tbody').on('dblclick', '.editable', function () 
    {
        var currentElement = $(this);
        var originalValue = currentElement.text().trim();

        // Permet de conserver le contenu actuel de la cellule quand on double-clique
        if (currentElement.find("input").length > 0) 
        {
            return;
        }

        var input = $("<input>", 
        {
            type: "text",
            value: originalValue,
            class: "form-control",
            css: {
                width: "100%",
                border: "1px solid #ccc",
                background: "white",
                padding: "2px",
            }
        });

        currentElement.html(input);
        input.focus().select();
        // blur = fermeture du champ, keydown = pression de touches
        input.on("blur keydown", function (e) {
            if (e.type === "blur" || e.key === "Enter") {
                var newValue = input.val().trim();
                if (newValue !== originalValue) 
                {
                    var field = currentElement.data("field");
                    var id = currentElement.closest("tr").data("id");

                    $.ajax({
                        url: "/user/update/" + id,
                        type: "POST",
                        data: { id: id, field: field, value: newValue },
                        success: function (response) {
                            if (response.success) {
                                currentElement.text(newValue);
                            } else {
                                currentElement.text(originalValue);
                                alert("Erreur: " + response.message);
                            }
                        },
                        error: function () {
                            currentElement.text(originalValue);
                            alert("Une erreur est survenue.");
                        }
                    });
                } 
                else 
                {
                    currentElement.text(originalValue);
                }
            } 
            else if (e.key === "Escape") 
            {
                currentElement.text(originalValue);
            }
        });
    });
});
