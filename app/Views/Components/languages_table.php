<h3>Tableau des Langues</h3>

<div class="table-container">
    <table id="languagesTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Abréviation</th>
                <th scope="col">Nom</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($languages as $language): ?>
                <tr data-id="<?= esc($language->abbreviation) ?>"> 
                    <td><?= esc($language->abbreviation) ?></td>
                    <td class="editable" data-field="languageName"><?= esc($language->languageName) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter une langue à la base de données</h3>

<div class="table-container">
    <form id="addLanguageForm">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Abréviation</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="abbreviation" class="form-control" required></td>
                    <td><input type="text" name="languageName" class="form-control" required></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {
        $("#addLanguageForm").on("submit", function (e) {
            e.preventDefault();

            $.ajax({
                url: "/dashboard/languages/add",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        let newRow = `<tr data-id="${response.data.abbreviation}">
                                        <td>${response.data.abbreviation}</td>
                                        <td class="editable" data-field="languageName">${response.data.languageName}</td>
                                      </tr>`;
                        $("#languagesTable tbody").append(newRow);
                        $("#addLanguageForm")[0].reset();
                    } else {
                        alert("Erreur: " + response.message);
                    }
                },
                error: function () {
                    alert("Une erreur est survenue.");
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        var table = $('#languagesTable').DataTable({
            "autoWidth": true,  
            "responsive": true  
        });

        $('#languagesTable tbody').on('dblclick', '.editable', function () {
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
                        var languageAbbreviation = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field"); 

                        $.ajax({
                            url: "/dashboard/languages/update",
                            type: "POST",
                            data: { abbreviation: languageAbbreviation, field: field, newValue: newValue },  
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