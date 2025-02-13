<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'espace Auteurs</h1>

<div class="table-container">
    <table id="authorsTable" class="table table-striped">
        <thead>
            <tr>
                <th>Auteurs</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalAuthors = count($authors);
            for ($i = 0; $i < $totalAuthors; $i += 2): 
            ?>
            <tr>
                <td>
                    <a href="#" class="author-link" data-id="<?= esc($authors[$i]->id) ?>">
                        <?= esc($authors[$i]->authorName) ?>
                    </a>
                </td>
                <td>
                    <?= isset($authors[$i+1]) ? '<a href="#" class="author-link" data-id="'.esc($authors[$i+1]->id).'">'.esc($authors[$i+1]->authorName).'</a>' : '' ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<h3>Ajouter un auteur à la base de données</h3>

<div class="table-container">
    <form action="/authors/add" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom de l'auteur</th>
                    <th>Action</th>
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

        // Modal servant à afficher la liste des livres d'un auteur
        $('#authorsTable tbody').on('click', '.author-link', function (e) {
            e.preventDefault(); // Empêche le comportement par défaut du lien
            var authorId = $(this).data('id');
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
