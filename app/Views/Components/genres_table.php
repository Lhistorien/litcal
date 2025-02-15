<h3>Tableau des Genres</h3>

<div class="table-container">
    <table id="genresTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Genre</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($genres as $genre): ?>
                <tr data-id="<?= esc($genre->id) ?>"> 
                    <td><?= esc($genre->id) ?></td>
                    <td class="editable" data-field="genreName"><?= esc($genre->genreName) ?></td>
                    <td class="editable" data-field="status"><?= esc($genre->status) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un genre à la base de données</h3>

<div class="table-container">
    <form action="/dashboard/genres/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom du genre</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="genreName" class="form-control" required></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {
        var table = $('#genresTable').DataTable({
            "autoWidth": true,
            "responsive": true
        });

        $('#genresTable tbody').on('dblclick', '.editable', function () {
            var currentElement = $(this);
            var originalValue = currentElement.text().trim();
            // Permet de conserver le contenu actuel de la cellule quand on double-clique
            if (currentElement.find("input").length > 0) {
                return;
            }

            var input = $("<input>", {
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
                if (e.type === "blur" || e.key === "Enter") 
                {
                    var newValue = input.val().trim();
                    if (newValue !== originalValue) 
                    {
                        var genreId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");

                        $.ajax({
                            url: "/dashboard/genres/update",
                            type: "POST",
                            data: { genreId: genreId, field: field, newValue: newValue },
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
</script>
