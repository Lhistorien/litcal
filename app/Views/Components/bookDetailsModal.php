<div class="modal fade" id="bookDetailsModal" tabindex="-1" aria-labelledby="bookDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="bookDetailsModalLabel">Détails du livre</h5>
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
        <?php if (!isset($book)): ?>
          <div class="container">
              <p>Chargement en cours...</p>
          </div>
        <?php else: ?>
          <div class="container">
              <div class="row">
                  <div class="col-md-4">
                      <img src="<?= base_url(!empty($book['cover']) ? $book['cover'] : 'cover/defaultcover.jpg') ?>" 
                           alt="Couverture du livre <?= esc($book['title']) ?>" 
                           class="img-fluid rounded shadow">
                  </div>
                  <div class="col-md-8">
                      <h3><?= esc($book['title']) ?></h3>
                      <?php 
                          // Séparer les auteurs principaux et les autres
                          $primaryAuthors = array_filter($book['authors'], function($author) {
                              return $author['role'] === 'Auteur';
                          });
                          $otherAuthors   = array_filter($book['authors'], function($author) {
                              return $author['role'] !== 'Auteur';
                          });
                      ?>
                      <?php if (!empty($primaryAuthors)): ?>
                          <p>
                              <strong><?= count($primaryAuthors) > 1 ? 'Auteurs' : 'Auteur' ?> :</strong>
                              <?php
                                  $primaryNames = array_map(function($author) {
                                      return esc($author['name']);
                                  }, $primaryAuthors);
                                  echo implode(', ', $primaryNames);
                              ?>
                          </p>
                      <?php endif; ?>
                      <?php if (!empty($otherAuthors)): ?>
                          <p>
                              <strong>Autres acteurs :</strong>
                              <?php
                                  $otherNames = array_map(function($author) {
                                      return esc($author['name']) . ' (' . esc($author['role']) . ')';
                                  }, $otherAuthors);
                                  echo implode(', ', $otherNames);
                              ?>
                          </p>
                      <?php endif; ?>
                      
                      <div class="row">
                          <div class="col-md-6">
                              <p>
                                  <strong>Éditeur :</strong> <?= esc($book['publisherName']) ?>
                                  <?php if (!empty($book['link'])): ?>
                                      <a href="<?= esc($book['link']) ?>" target="_blank" title="Voir sur le site officiel">
                                          <i class="fas fa-external-link-alt"></i>
                                      </a>
                                  <?php endif; ?>
                              </p>
                          </div>
                          <div class="col-md-6">
                              <p><strong>Langue :</strong> <?= esc($book['languageAbbreviation']) ?></p>
                          </div>
                      </div>
                      
                      <div class="row">
                          <div class="col-md-6">
                              <p><strong>Date de publication :</strong> <?= date('d/m/y', strtotime($book['publication'])) ?></p>
                          </div>
                          <div class="col-md-6">
                              <p><strong>Prix :</strong> <?= isset($book['price']) ? number_format((float)$book['price'], 2, ',', '') : 'Non renseigné' ?> €</p>
                          </div>
                      </div>
                      
                      <div class="row">
                          <div class="col-md-6">
                              <p><strong>Format :</strong> <?= esc($book['format']) ?></p>
                          </div>
                          <div class="col-md-6">
                              <p><strong>ISBN :</strong> <?= esc($book['isbn'] ?? 'Non renseigné') ?></p>
                          </div>
                      </div>
                      
                      <p><strong>Genres :</strong> <?= implode(', ', $book['genres']) ?></p>
                      <?php if (!empty($book['subgenres'])): ?>
                          <p><strong>Sous-genres :</strong> <?= implode(', ', $book['subgenres']) ?></p>
                      <?php endif; ?>
                  </div>
              </div>
              
              <div class="row mt-3">
                  <div class="col">
                      <h5>Résumé</h5>
                      <p><?= esc($book['description'] ?? 'Non renseigné') ?></p>
                  </div>
              </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
