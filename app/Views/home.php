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
                  <div class="card">
                    <img src="<?= esc($book->cover) ?>" class="card-img-top img-fluid" alt="<?= esc($book->title) ?>">
                    <div class="card-body p-2">
                      <p class="card-text small"><?= esc($book->title) ?></p>
                    </div>
                  </div>
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
<h2>À parraître</h2>
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
                  <div class="card">
                    <img src="<?= esc($book->cover) ?>" class="card-img-top img-fluid" alt="<?= esc($book->title) ?>">
                    <div class="card-body p-2">
                      <p class="card-text small"><?= esc($book->title) ?></p>
                    </div>
                  </div>
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

<style>
  /* Affiche l'image en entier sans la découper */
  .card-img-top {
      max-height: 150px; /* Ajustez cette valeur selon vos besoins */
      width: 100%;
      object-fit: contain;
  }

  /* Inverser les couleurs et ajouter un fond aux icônes */
  .carousel-control-prev-icon,
  .carousel-control-next-icon {
      filter: invert(100%);
      background-color: rgba(0, 0, 0, 0.5);
      border-radius: 50%;
      padding: 10px;
  }

  /* Conteneur pour autoriser le débordement des contrôles */
  .carousel-container {
      position: relative;
      overflow: visible;
  }

  /* Décaler les boutons de contrôle à l'extérieur */
  .carousel-control-prev {
      left: -40px !important;
  }
  .carousel-control-next {
      right: -40px !important;
  }
</style>

<?= $this->endSection() ?>
