<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'éditeur de livres</h1>

<button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addBookModal">
    Ajouter un livre
</button>

<div class="table-container">
    <table id="booksTable" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Titre</th>
                <th scope="col">Auteurs</th>
                <th scope="col">Éditeur</th>
                <th scope="col">Date de publication</th>
                <th scope="col">Langue</th>
                <th scope="col">Genre</th>
                <th scope="col">Sous-Genre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= esc($book['id']) ?></td>
                    <td>
                        <a href="<?= site_url('book/' . esc($book['id'])) ?>">
                            <?= esc($book['title']) ?>
                        </a>
                    </td>
                    <td>
                        <?php
                        $authors = array_filter($book['authors'], function($author) {
                            return $author['role'] === 'Auteur';  
                        });
                        
                        $authorNames = array_map(function($author) {
                            return esc($author['name']);
                        }, $authors);
                        echo implode(', ', $authorNames);
                        ?>
                    </td>
                    <td><?= esc($book['publisherName']) ?></td>
                    <td><?= date('d/m/Y', strtotime($book['publication'])) ?></td>
                    <td title="<?= esc($book['languageName']) ?>">
                        <?= esc($book['languageAbbreviation']) ?>
                    </td>
                    <td><?= implode(', ', esc($book['genres'])) ?></td>
                    <td><?= implode(', ', esc($book['subgenres'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->include('components/addBookModal') ?>

<script>
    $(document).ready(function () {
        $('#genre').select2({
            dropdownParent: $('#addBookModal')  
        }).trigger('change');

        $('#subgenre').select2({
            dropdownParent: $('#addBookModal') 
        }).trigger('change'); 
        $('#author').select2({
            dropdownParent: $('#addBookModal') 
        }).trigger('change'); 

        $('.select2-container').css('width', '100%');

        $('#booksTable').DataTable({
            "autoWidth": true,
            "responsive": true,
            "language": {
                "sProcessing":     "Traitement en cours...",
                "sSearch":         "Rechercher :",
                "sLengthMenu":     "Afficher _MENU_ entrées",
                "sInfo":           "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "sInfoEmpty":      "Affichage de 0 à 0 sur 0 entrée",
                "sInfoFiltered":   "(filtré à partir de _MAX_ entrées au total)",
                "sLoadingRecords": "Chargement en cours...",
                "sZeroRecords":    "Aucune entrée correspondante trouvée",
                "sEmptyTable":     "Aucune donnée disponible dans le tableau",
                "oPaginate": {
                    "sFirst":    "Premier",
                    "sPrevious": "Précédent",
                    "sNext":     "Suivant",
                    "sLast":     "Dernier"
                },
                "oAria": {
                    "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    let contributorCount = 1;

    document.getElementById("add-people-btn").addEventListener("click", function() {
        var additionalContributors = document.getElementById("additional-contributors");
        additionalContributors.style.display = "block";
        this.style.display = "none"; 
    });

    document.getElementById('additional-contributors').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('add-more-people')) {
            contributorCount++;
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mt-3');
            newRow.setAttribute('id', `contributor-${contributorCount}`);

            newRow.innerHTML = `
                <div class="col-md-6 mb-3">
                    <label for="actor_name_${contributorCount}" class="form-label">Nom de l'acteur</label>
                    <select class="form-select" id="actor_name_${contributorCount}" name="actor_name[]">
                        <option value="">Sélectionner un auteur</option>
                        <?php foreach ($listofauthors as $author): ?>
                            <option value="<?= esc($author->id) ?>"><?= esc($author->authorName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="actor_role_${contributorCount}" class="form-label">Rôle de l'acteur</label>
                    <div class="d-flex align-items-center">
                        <select class="form-select" id="actor_role_${contributorCount}" name="actor_role[]">
                            <option value="">Sélectionner un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <?php if ($role->roleName !== 'Writer'): ?>
                                    <option value="<?= esc($role->id) ?>"><?= esc($role->roleName) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline-secondary add-more-people ms-2" id="add-more-btn-${contributorCount}">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('additional-contributors').appendChild(newRow);

            e.target.style.display = "none";

            document.getElementById(`add-more-btn-${contributorCount}`).addEventListener('click', function() {
                document.getElementById('additional-contributors').dispatchEvent(new Event('click'));
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
