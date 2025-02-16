<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h2>Édition du livre</h2>

<form id="editBookForm" action="<?= site_url('book/updateBook') ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="redirect_url" value="<?= isset($_GET['redirect']) ? $_GET['redirect'] : '/books' ?>">
    <input type="hidden" name="bookId" value="<?= esc($book['id']) ?>">

    <div class="mb-3">
        <label for="title" class="form-label">Titre</label>
        <input type="text" class="form-control" id="title" name="title" required value="<?= esc($book['title']) ?>">
    </div>

    <?php 
        // Filtrer pour obtenir uniquement les auteurs principaux (rôle auteur : id 1 ou "Auteur")
        $primaryAuthors = array_filter($book['authors'], function($author) {
            return ($author['role'] == 1 || strtolower($author['role']) == 'auteur');
        });
        $selectedPrimaryIds = array_column($primaryAuthors, 'id');
    ?>
    <div class="mb-3">
        <label for="author" class="form-label">Auteur(s)</label>
        <select class="form-select select2" id="author" name="author[]" multiple required>
            <?php foreach ($listofauthors as $author): ?>
                <option value="<?= esc($author->id) ?>"
                    <?= in_array($author->id, $selectedPrimaryIds) ? 'selected' : '' ?>>
                    <?= esc($author->authorName) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="button" id="add-people-btn" class="btn btn-primary mb-4">Ajouter d'autres acteurs</button>

    <?php 
        // Filtrer pour obtenir uniquement les acteurs additionnels (rôle différent de 1)
        $additionalActors = array_filter($book['authors'], function($author) {
            return !($author['role'] == 1 || strtolower($author['role']) == 'auteur');
        });
    ?>
    <div id="additional-contributors" style="display: <?= (count($additionalActors) > 0) ? 'block' : 'none'; ?>">
        <?php foreach ($additionalActors as $index => $actor): ?>
            <div class="row mt-3" id="contributor-<?= $index + 1 ?>">
                <div class="col-md-6 mb-3">
                    <label for="actor_name_<?= $index + 1 ?>" class="form-label">Nom de l'acteur</label>
                    <select class="form-select" id="actor_name_<?= $index + 1 ?>" name="actor_name[]">
                        <option value="">Sélectionner un auteur</option>
                        <?php foreach ($listofauthors as $author): ?>
                            <option value="<?= esc($author->id) ?>"
                                <?= ($actor['id'] == $author->id) ? 'selected' : '' ?>>
                                <?= esc($author->authorName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="actor_role_<?= $index + 1 ?>" class="form-label">Rôle de l'acteur</label>
                    <div class="d-flex align-items-center">
                        <select class="form-select" id="actor_role_<?= $index + 1 ?>" name="actor_role[]">
                            <option value="">Sélectionner un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <?php if ($role->id !== 1): // exclure le rôle auteur ?>
                                    <option value="<?= esc($role->id) ?>"
                                        <?= ($actor['role'] == $role->roleName || $actor['role'] == $role->id) ? 'selected' : '' ?>>
                                        <?= esc($role->roleName) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <!-- Bouton pour supprimer cet acteur additionnel -->
                        <button type="button" class="btn btn-danger btn-sm ms-2 remove-contributor" data-contributor-id="<?= $index + 1 ?>">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="publisher" class="form-label">Éditeur</label>
            <select class="form-select" id="publisher" name="publisher" required>
                <option value="">Sélectionner un éditeur</option>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?= esc($publisher->id) ?>"
                        <?= ($publisher->id == $book['publisherId']) ? 'selected' : '' ?>>
                        <?= esc($publisher->publisherName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="publication" class="form-label">Date de publication</label>
            <input type="date" class="form-control" id="publication" name="publication" required value="<?= esc($book['publication']) ?>">
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-end">
            <div>
                <label for="preorder" class="form-check-label ms-1" style="font-size: 1.1rem;">Précommande :</label>
                <input type="checkbox" class="form-check-input ms-2" id="preorder" name="preorder" value="1" style="transform: scale(1.3);"
                    <?= ($book['preorder']) ? 'checked' : '' ?>>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="language" class="form-label">Langue</label>
            <select class="form-select" id="language" name="language" required>
                <option value="">Sélectionner une langue</option>
                <?php foreach ($languages as $language): ?>
                    <option value="<?= esc($language->abbreviation) ?>"
                        <?= ($language->abbreviation == $book['languageAbbreviation']) ? 'selected' : '' ?>>
                        <?= esc($language->languageName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label for="price" class="form-label">Prix (en €)</label>
            <input type="text" class="form-control" id="price" name="price" placeholder="9,99 €" value="<?= esc($book['price']) ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" id="isbn" name="isbn" value="<?= esc($book['isbn']) ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label for="format" class="form-label">Format</label>
            <select class="form-select" id="format" name="format" required>
                <option value="">Choisir le format</option>
                <?php foreach ($formats as $format): ?>
                    <option value="<?= esc($format->format) ?>"
                        <?= ($format->format == $book['format']) ? 'selected' : '' ?>>
                        <?= esc($format->format) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3 row">
        <div class="col-md-9">
            <label for="serie" class="form-label">Série</label>
            <select class="form-select" id="serie" name="serie">
                <option value="">Sélectionner une série</option>
                <?php foreach ($series as $serie): ?>
                    <option value="<?= esc($serie->id) ?>"
                        <?= (isset($book['serieId']) && $serie->id == $book['serieId']) ? 'selected' : '' ?>>
                        <?= esc($serie->serieName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="volume" class="form-label">Tome</label>
            <input type="text" class="form-control" id="volume" name="volume" placeholder="Numéro" value="<?= esc($book['volume']) ?>">
        </div>
    </div>
    <p style="text-align: right;">Écrivez HS (pour hors-série) s'il n'y a pas de n° de tome et I pour les intégrales</p>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="genre" class="form-label">Genre(s)</label>
            <select class="form-select select2" id="genre" name="genre[]" multiple>
                <?php 
                $selectedGenres = array_column($book['genres'], 'id'); 
                ?>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?= esc($genre->id) ?>"
                        <?= in_array($genre->id, $selectedGenres) ? 'selected' : '' ?>>
                        <?= esc($genre->genreName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="subgenre" class="form-label">Sous-Genre(s)</label>
            <select class="form-select select2" id="subgenre" name="subgenre[]" multiple>
                <?php 
                $selectedSubgenres = array_column($book['subgenres'], 'id'); 
                ?>
                <?php foreach ($subgenres as $subgenre): ?>
                    <option value="<?= esc($subgenre->id) ?>"
                        <?= in_array($subgenre->id, $selectedSubgenres) ? 'selected' : '' ?>>
                        <?= esc($subgenre->subgenreName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="link" class="form-label">Lien externe</label>
            <input type="text" class="form-control" id="link" name="link" value="<?= esc($book['link']) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Couverture</label>
            <?php if (!empty($book['cover'])): ?>
                <div class="mb-2">
                    <img src="<?= base_url($book['cover']) ?>" alt="Couverture" style="max-width: 150px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Résumé</label>
        <textarea class="form-control" id="description" name="description"><?= esc($book['description']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour</button>
</form>

<script>
    $(document).ready(function(){
        $('.select2').select2();
    });

    $(document).on('click', '.remove-contributor', function() {
        var contributorId = $(this).data('contributor-id');
        $('#contributor-' + contributorId).remove();
    });
</script>
<script>
$(document).ready(function(){
    $('.select2').select2();

    // Initialiser le compteur sur le nombre actuel d'acteurs additionnels
    var contributorCount = <?= count($additionalActors) ?>;
    
    // Lors du clic sur "Ajouter d'autres acteurs"
    $('#add-people-btn').on('click', function(){
        contributorCount++;
        // Afficher le conteneur s'il est caché
        $('#additional-contributors').show();
        
        // Construire la nouvelle ligne HTML avec exclusion du rôle Auteur (id 1)
        var newRow = `
            <div class="row mt-3" id="contributor-${contributorCount}">
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
                                <?php if ((int)$role->id !== 1): ?>
                                    <option value="<?= esc($role->id) ?>"><?= esc($role->roleName) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-danger btn-sm ms-2 remove-contributor" data-contributor-id="${contributorCount}">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Ajouter la nouvelle ligne dans le conteneur
        $('#additional-contributors').append(newRow);
        // Réinitialiser Select2 pour les nouveaux éléments
        $('#actor_name_' + contributorCount).select2();
        $('#actor_role_' + contributorCount).select2();
    });

    // Gestion de la suppression d'un acteur additionnel
    $(document).on('click', '.remove-contributor', function() {
        var contributorId = $(this).data('contributor-id');
        $('#contributor-' + contributorId).remove();
    });
});
</script>




<?= $this->endSection() ?>
