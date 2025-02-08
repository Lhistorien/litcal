<h3>Tableau des Éditeurs</h3>

<div class="table-container">
    <table id="publishersTable" class="table table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nom</th>
            <th scope="col">Site Web</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($publishers as $publisher): ?>
            <tr data-id="<?= esc($publisher->id) ?>"> 
                <td><?= esc($publisher->id) ?></td>
                <td class="editable" data-field="publisherName"><?= esc($publisher->publisherName) ?></td>
                <td class="editable" data-field="website">
                    <?php if (!empty($publisher->website)): ?>
                        <a href="<?= esc($publisher->website) ?>" target="_blank"><?= esc($publisher->website) ?></a>
                    <?php else: ?>
                        <em>Aucun</em>
                    <?php endif; ?>
                </td>
                <td class="editable" data-field="status"><?= esc($publisher->status) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>

<h3>Ajouter un éditeur à la base de données</h3>

<div class="table-container">
    <form action="/dashboard/publishers/add" method="POST">
        <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Nom de l'éditeur</th>
                <th scope="col">Site Web</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" name="publisherName" class="form-control" required></td>
                <td><input type="url" name="website" class="form-control"></td>
                <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
            </tr>
        </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {
        var table = $('#publishersTable').DataTable({
            "autoWidth": true,  
            "responsive": true  
        });

        $('#publishersTable tbody').on('dblclick', '.editable', function () {
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
                if (e.type === "blur" || e.key === "Enter") 
                {
                    var newValue = input.val().trim();
                    if (newValue !== originalValue) 
                    {
                        var publisherId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");

                        $.ajax({
                            url: "/dashboard/publishers/update",
                            type: "POST",
                            data: { publisherId: publisherId, field: field, newValue: newValue },  
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
