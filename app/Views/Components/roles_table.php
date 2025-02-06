<h3>Tableau des Rôles</h3>

<div class="table-container">
    <table id="rolesTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $role): ?>
                <tr data-id="<?= esc($role->id) ?>"> 
                    <td><?= esc($role->id) ?></td>
                    <td class="editable" data-field="roleName"><?= esc($role->roleName) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un rôle à la base de données</h3>

<div class="table-container">
    <form action="/dashboard/roles/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom du rôle</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="roleName" class="form-control" required></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {
        var table = $('#rolesTable').DataTable({
            "autoWidth": true,  
            "responsive": true  
        });

        $('#rolesTable tbody').on('dblclick', '.editable', function () {
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
                        var roleId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");

                        $.ajax({
                            url: "/dashboard/roles/update",
                            type: "POST",
                            data: { roleId: roleId, newRoleName: newValue },  
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
