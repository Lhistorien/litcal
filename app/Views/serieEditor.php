<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'éditeur de série</h1>

<div class="table-container">
    <table id="seriesTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Série</th>
                <th scope="col">Status</th>
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
                if (e.type === "blur" || e.key === "Enter") 
                {
                    var newValue = input.val().trim();
                    if (newValue !== originalValue) 
                    {
                        var serieId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");

                        $.ajax({
                            url: "/series/update",
                            type: "POST",
                            data: { serierId: serieId, field: field, newValue: newValue },  
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

<?= $this->endSection() ?>
