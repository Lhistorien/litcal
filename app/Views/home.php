<?= $this->extend('Layouts/main_layout') ?>
<?= $this->section('content') ?>

<h1>Accueil</h1>

<!-- Carousel pour les livres sortis ces 30 derniers jours -->
<h2>Dernières sorties</h2>
<?php if(!empty($recentBooks)): ?>
  <?php $chunksRecent = array_chunk($recentBooks, 6); ?>
  <div class="carousel-container">
    <div id="recentCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach($chunksRecent as $index => $chunk): ?>
          <div class="carousel-item <?= ($index == 0 ? 'active' : '') ?>">
            <div class="row">
              <?php foreach($chunk as $book): ?>
                <div class="col-2">
                  <a href="#" class="book-link" data-id="<?= esc($book->id) ?>">
                    <div class="card">
                      <img src="<?= esc($book->cover) ?>" class="card-img-top img-fluid" alt="<?= esc($book->title) ?>">
                      <div class="card-body p-2">
                        <p class="card-text small"><?= esc($book->title) ?></p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#recentCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#recentCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>
  </div>
<?php else: ?>
  <p>Aucun livre récent à afficher.</p>
<?php endif; ?>

<!-- Carousel pour les livres à paraître dans les 30 prochains jours -->
<h2>À paraître</h2>
<?php if(!empty($upcomingBooks)): ?>
  <?php $chunksUpcoming = array_chunk($upcomingBooks, 6); ?>
  <div class="carousel-container">
    <div id="upcomingCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach($chunksUpcoming as $index => $chunk): ?>
          <div class="carousel-item <?= ($index == 0 ? 'active' : '') ?>">
            <div class="row">
              <?php foreach($chunk as $book): ?>
                <div class="col-2">
                  <a href="#" class="book-link" data-id="<?= esc($book->id) ?>">
                    <div class="card">
                      <img src="<?= esc($book->cover) ?>" class="card-img-top img-fluid" alt="<?= esc($book->title) ?>">
                      <div class="card-body p-2">
                        <p class="card-text small"><?= esc($book->title) ?></p>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#upcomingCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#upcomingCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>
  </div>
<?php else: ?>
  <p>Aucun livre à paraître prochainement.</p>
<?php endif; ?>

<!-- Modal pour afficher les détails d'un livre -->
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
        <p>Chargement en cours...</p>
      </div>
    </div>
  </div>
</div>

<style>
  .card-img-top {
      max-height: 150px; 
      width: 100%;
      object-fit: contain;
  }

  .carousel-control-prev-icon,
  .carousel-control-next-icon {
      filter: invert(100%);
      background-color: rgba(0, 0, 0, 0.5);
      border-radius: 50%;
      padding: 10px;
  }

  .carousel-container {
      position: relative;
      overflow: visible;
  }

  .carousel-control-prev {
      left: -40px !important;
  }
  .carousel-control-next {
      right: -40px !important;
  }
</style>


<!-- Inclusion du script externe pour la gestion des actions sur les livres pour plus de lisibilité -->
<script src="/js/bookActions.js"></script>

<?= $this->endSection() ?>
