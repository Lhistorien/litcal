<!-- Contenu de l'onglet général -->
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
