<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBookModalLabel">Ajouter un livre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addBookForm" action="<?= site_url('books/add') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Auteur(s)</label>
                        <!-- select2 permet de sélectionner plusieurs éléments dans un seul champ de formulaire -->
                        <select class="form-select select2" id="author" name="author[]" multiple required>
                            <?php foreach ($listofauthors as $author): ?>
                                <option value="<?= esc($author->id) ?>"><?= esc($author->authorName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bouton pour afficher des champs supplémentaires pour ajouter d'autres acteurs -->
                    <button type="button" id="add-people-btn" class="btn btn-primary mb-4">Ajouter d'autres acteurs</button>

                    <!-- Section cachée par défaut pour les contributeurs additionnels -->
                    <div id="additional-contributors" style="display: none;">
                        <div class="row mt-3" id="contributor-1">
                            <div class="col-md-6 mb-3">
                                <label for="actor_name" class="form-label">Nom de l'acteur</label>
                                <select class="form-select" id="actor_name" name="actor_name[]">
                                    <option value="">Sélectionner un auteur</option>
                                    <?php foreach ($listofauthors as $author): ?>
                                        <option value="<?= esc($author->id) ?>"><?= esc($author->authorName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actor_role" class="form-label">Rôle de l'acteur</label>
                                <div class="d-flex align-items-center">
                                    <select class="form-select" id="actor_role" name="actor_role[]">
                                        <option value="">Sélectionner un rôle</option>
                                        <?php foreach ($roles as $role): ?>
                                            <?php if ($role->id !== 1): ?>
                                                <option value="<?= esc($role->id) ?>"><?= esc($role->roleName) ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Bouton pour ajouter dynamiquement un nouvel ensemble de champs contributeur -->
                                    <button type="button" class="btn btn-outline-secondary add-more-people ms-2" id="add-more-btn">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publisher" class="form-label">Éditeur</label>
                            <select class="form-select" id="publisher" name="publisher" required>
                                <option value="">Sélectionner un éditeur</option>
                                <?php foreach ($publishers as $publisher): ?>
                                    <option value="<?= esc($publisher->id) ?>"><?= esc($publisher->publisherName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="publication" class="form-label">Date de publication</label>
                            <input type="date" class="form-control" id="publication" name="publication" required>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div>
                                <label for="preorder" class="form-check-label ms-1" style="font-size: 1.1rem;">Précommande : </label>
                                <input type="checkbox" class="form-check-input ms-2" id="preorder" name="preorder" value="1" style="transform: scale(1.3);">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="language" class="form-label">Langue</label>
                            <select class="form-select" id="language" name="language" required>
                                <option value="">Sélectionner une langue</option>
                                <?php foreach ($languages as $language): ?>
                                    <option value="<?= esc($language->abbreviation) ?>"><?= esc($language->languageName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="price" class="form-label">Prix (en €)</label>
                            <input type="text" class="form-control" id="price" name="price" placeholder="9,99 €">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="format" class="form-label">Format</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="">Choisir le format</option>
                                <?php foreach ($formats as $format): ?>
                                    <option value="<?= esc($format->format) ?>"><?= esc($format->format) ?></option>
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
                                    <option value="<?= esc($serie->id) ?>"><?= esc($serie->serieName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="volume" class="form-label">Tome</label>
                            <input type="text" class="form-control" id="volume" name="volume" placeholder="Numéro">
                        </div>
                    </div>
                    <p style="text-align: right;">Écrivez HS (pour hors-série) s'il n'y a pas de n° de tome et I pour les intégrales</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="genre" class="form-label">Genre(s)</label>
                            <select class="form-select select2" id="genre" name="genre[]" multiple>
                                <?php foreach ($genres as $genre): ?>
                                    <option value="<?= esc($genre->id) ?>"><?= esc($genre->genreName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subgenre" class="form-label">Sous-Genre(s)</label>
                            <select class="form-select select2" id="subgenre" name="subgenre[]" multiple>
                                <?php foreach ($subgenres as $subgenre): ?>
                                    <option value="<?= esc($subgenre->id) ?>"><?= esc($subgenre->subgenreName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="link" class="form-label">Lien externe</label>
                            <input type="textarea" class="form-control" id="link" name="link">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Couverture</label>
                            <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
                        </div>   
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Résumé</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>        
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>
