<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'espace Livres</h1>

<!-- Bouton pour ajouter un livre réservé aux admins et contributeurs -->
<?php if (session()->get('user_role') === 'Administrator' || session()->get('user_role') === 'Contributor'): ?>
    <button type="button" class="btn btn-primary float-end" mb-3 data-bs-toggle="modal" data-bs-target="#addBookModal">
        Ajouter un livre
    </button>
<?php endif; ?>

<div class="clearfix"></div>
<div class="table-container">
    <table id="booksTable" class="table table-striped">
        <thead>
            <tr>
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
                <!-- N'affiche que les livres actifs (status = 1) -->
                <?php if ($book['status'] == 1): ?>
                    <tr>
                        <td>
                            <a href="#" class="book-link" data-id="<?= esc($book['id']) ?>">
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
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->include('components/addBookModal') ?>

<!-- Modal pour afficher les détails d'un livre -->
<div class="modal fade" id="bookDetailsModal" tabindex="-1" aria-labelledby="bookDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="bookDetailsModalLabel">Détails du livre</h5>
            <!-- Bouton pour ouvrir l'éditeur réservé aux admins et contributeurs -->
            <?php if (session()->get('user_role') === 'Administrator' || session()->get('user_role') === 'Contributor'): ?>
                <a id="editBookBtn" href="#" class="btn btn-warning btn-sm ms-3" title="Modifier ce livre">
                    Modifier
                </a>
                <button id="deactivateBookBtn" type="button" class="btn btn-danger btn-sm ms-3 me-2" title="Désactiver ce livre">
                    <i class="fas fa-times"></i>
                </button>
            <?php endif; ?>
            <?php if (session()->get('is_logged_in')): ?>
                <button id="subscribeBookBtn" type="button" class="btn btn-success btn-sm ms-3" title="Suivre ce livre">
                    Suivre ce livre
                </button>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
            <p>Chargement en cours...</p>
        </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function () {
        // select2 permet de sélectionner plusieurs éléments dans un seul champ de formulaire
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

        // Traduit la DataTable en français
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

    // Utilisation de DOMContentLoaded pour s'assurer que l'intégralité du DOM est chargée avant d'attacher les listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation d'un compteur pour attribuer un ID unique à chaque ensemble de champs acteurs (puisqu'il peut y en avoir un nombre indéfini)
        let contributorCount = 1;

        // Lors du clic sur le bouton "add-people-btn", afficher la section des contributeurs additionnels et masquer ce bouton
        document.getElementById("add-people-btn").addEventListener("click", function() {
            var additionalContributors = document.getElementById("additional-contributors");
            additionalContributors.style.display = "block";
            // Masquer le bouton après clic pour éviter les clics répétés inutiles
            this.style.display = "none"; 
        });

        // Ajoute un écouteur sur le conteneur des contributeurs additionnels
        // Cet écouteur détecte un clic sur n'importe quel élément à l'intérieur, notamment les boutons "add-more-people"
        document.getElementById('additional-contributors').addEventListener('click', function(e) {
            // Vérifie que l'élément cliqué possède la classe 'add-more-people'
            if (e.target && e.target.classList.contains('add-more-people')) {
                // Incrémente le compteur pour le nouvel ensemble de champs
                contributorCount++;
                
                // Crée dynamiquement une nouvelle div qui contiendra les champs pour un contributeur supplémentaire
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'mt-3'); // Ajoute les classes Bootstrap pour la mise en page
                newRow.setAttribute('id', `contributor-${contributorCount}`); // Attribue un identifiant unique à la nouvelle ligne

                // Ajoute le HTML nécessaire pour sélectionner l'acteur et son rôle, avec des IDs dynamiques
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
                            <!-- Bouton pour ajouter un autre ensemble de contributeurs -->
                            <button type="button" class="btn btn-outline-secondary add-more-people ms-2" id="add-more-btn-${contributorCount}">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                `;

                // Ajoute la nouvelle ligne créée dans le conteneur des contributeurs additionnels
                document.getElementById('additional-contributors').appendChild(newRow);

                // Masque le bouton "add-more-people" qui a été cliqué pour éviter d'ajouter plusieurs fois pour le même contributeur
                e.target.style.display = "none";

                // Attache un écouteur sur le nouveau bouton ajouté pour permettre l'ajout d'un autre contributeur lors d'un futur clic
                document.getElementById(`add-more-btn-${contributorCount}`).addEventListener('click', function() {
                    // Déclenche un événement de clic sur le conteneur pour réutiliser l'écouteur et ajouter un nouveau champ
                    document.getElementById('additional-contributors').dispatchEvent(new Event('click'));
                });
            }
        });
    });
</script>

<script src="/js/bookActions.js"></script>

<?= $this->endSection() ?>
