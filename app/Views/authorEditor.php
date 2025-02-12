<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'éditeur d'auteurs</h1>

<div class="table-container">
    <table id="authorsTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Auteur</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authors as $author): ?>
                <tr data-id="<?= esc($author->id) ?>"> 
                    <!-- Ajout de la classe "author-id" pour le clic -->
                    <td class="author-id"><?= esc($author->id) ?></td>
                    <td class="editable" data-field="authorName"><?= esc($author->authorName) ?></td>
                    <td class="editable" data-field="status"><?= esc($author->status) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un auteur à la base de données</h3>

<div class="table-container">
    <form action="/authors/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Nom de l'auteur</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="authorName" class="form-control" required></td>
                    <td><button type="submit" class="btn btn-primary">Ajouter</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<!-- Modal pour afficher le profil d'un auteur -->
<div class="modal fade" id="booksModal" tabindex="-1" aria-labelledby="booksModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="booksModalLabel">Livres liés à l'auteur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <p>Chargement...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>


<script>
    $(document).ready(function () {
        console.log('jQuery est chargé et document.ready exécuté.');

        var table = $('#authorsTable').DataTable({
            "autoWidth": true,  
            "responsive": true  
        });

        // Edition en ligne par double-clic sur les cellules éditables
        $('#authorsTable tbody').on('dblclick', '.editable', function () {
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

                    if (currentElement.data("field") === 'status') {
                        newValue = parseInt(newValue, 10);  
                    }

                    if (currentElement.data("field") === 'status' && (newValue !== 0 && newValue !== 1)) {
                        alert("La valeur du statut doit être 0 ou 1.");
                        return;  
                    }
                    if (newValue !== originalValue) {
                        var authorId = currentElement.closest("tr").data("id");
                        var field = currentElement.data("field");
                        console.log('Nouvelle valeur pour le champ ' + field + ':', newValue);
                        $.ajax({
                            url: "/authors/update",
                            type: "POST",
                            data: { authorId: authorId, field: field, newValue: newValue },
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

        // Modal servant à afficher la liste des livres d'un auteur
        $('#authorsTable tbody').on('click', '.author-id', function () {
            var authorId = $(this).closest('tr').data('id');
            console.log('ID de l\'auteur cliqué:', authorId);
            $.ajax({
                url: '/getAuthorBooks', 
                type: 'POST',
                data: { id: authorId },
                success: function(response) {
                    console.log('Réponse AJAX reçue:', response);
                    $('#booksModal .modal-body').html(response);
                    $('#booksModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX: ', error);
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>
