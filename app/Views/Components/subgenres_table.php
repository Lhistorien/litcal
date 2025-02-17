<h3>Tableau des Sous-genres</h3>

<div class="table-container">
    <table id="subGenresTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Sous-genre</th>
                <th scope="col">Genre(s) associé(s)</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subgenres as $subgenre): ?>
                <tr data-id="<?= esc($subgenre->id) ?>">
                    <td><?= esc($subgenre->id) ?></td>
                    <td class="editable" data-field="subgenreName"><?= esc($subgenre->subgenreName) ?></td>
                    <!-- Champs affichant tous les genres associés à ce sous-genre -->
                    <td><?= esc($subgenre->genres ?? 'Aucun genre associé') ?></td> 
                    <td class="editable" data-field="status"><?= esc($subgenre->status) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un sous-genre à la base de données</h3>

<div class="table-container">
    <form action="/dashboard/subgenres/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom du sous-genre</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="subgenreName" class="form-control" required></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<h3>Associer un sous-genre à un genre</h3>

<div class="table-container">
    <form action="/dashboard/subgenres/associate" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Sous-genre</th>
                    <th scope="col">Genre</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="subgenre" class="form-control" required>
                            <option value="" disabled selected>Choisir un sous-genre</option>
                            <?php foreach ($subgenres as $subgenre): ?>
                                <option value="<?= esc($subgenre->id) ?>"><?= esc($subgenre->subgenreName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="genre" class="form-control" required>
                            <option value="" disabled selected>Choisir un genre</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= esc($genre->id) ?>"><?= esc($genre->genreName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Associer</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#subGenresTable').DataTable();
        
    });

    $('#subGenresTable tbody').on('dblclick', '.editable', function () {
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
            if (e.type === "blur" || e.key === "Enter") {
                var newValue = input.val().trim();
                if (newValue !== originalValue) {
                    var subgenreId = currentElement.closest("tr").data("id");
                    var field = currentElement.data("field");

                    $.ajax({
                        url: "/dashboard/subgenres/update",
                        type: "POST",
                        data: { subgenreId: subgenreId, field: field, newValue: newValue },
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
                } else {
                    currentElement.text(originalValue);
                }
            } else if (e.key === "Escape") {
                currentElement.text(originalValue);
            }
        });
    });
</script>
