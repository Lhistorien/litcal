<h3>Tableau des Auteurs</h3>

<div class="table-container">
    <table id="seriesTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nom de la série</th>
                <th scope="col">Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($series as $serie): ?>
                <tr data-id="<?= esc($serie->id) ?>">
                    <td><?= esc($serie->id) ?></td>
                    <td class="editable" data-field="serieName"><?= esc($serie->serieName) ?></td>
                    <td class="editable" data-field="status"><?= esc($serie->status) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un auteur à la base de données</h3>

<div class="table-container">
    <form action="/dashboard/series/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom de la série</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="serieName" class="form-control" required></td>
                    <td><input type="text" name="comment" class="form-control"></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {
        var table = $('#seriesTable').DataTable({
            "autoWidth": true,
            "responsive": true
        });

        $('#seriesTable tbody').on('dblclick', '.editable', function () {
            var currentElement = $(this);
            var originalValue = currentElement.text().trim();

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

            input.on("blur keydown", function (e) {
                if (e.type === "blur" || e.key === "Enter") {
                    var newValue = input.val().trim();
                    if (newValue !== originalValue) {
                        var serieId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");

                        $.ajax({
                            url: "/dashboard/series/update",
                            type: "POST",
                            data: { serieId: serieId, field: field, newValue: newValue },
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
    });
</script>
