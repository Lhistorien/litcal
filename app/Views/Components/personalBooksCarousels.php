<!-- Components/personalBooksCarousel.php -->
<?php if (session()->get('is_logged_in')): ?>
  <?php
    // Filtrer les livres récents pour la sélection personnelle
    // On suppose que chaque livre (objet BookEntity) possède désormais la propriété "labels" enrichie via BookModel::enrichBooksWithLabels()
    $personalRecent = array_filter($recentBooks, function($book) use ($userSubscriptions) {
        if (!empty($book->labels) && is_array($book->labels)) {
            foreach ($book->labels as $labelId) {
                if (in_array($labelId, $userSubscriptions)) {
                    return true;
                }
            }
        }
        return false;
    });
    $chunksPersonalRecent = array_chunk($personalRecent, 6);
  ?>
  <h2>Dernières sorties (sélection personnelle)</h2>
  <?php if (!empty($personalRecent)): ?>
    <div class="carousel-container">
      <div id="personalRecentCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php foreach ($chunksPersonalRecent as $index => $chunk): ?>
            <div class="carousel-item <?= ($index == 0 ? 'active' : '') ?>">
              <div class="row">
                <?php foreach ($chunk as $book): ?>
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
        <button class="carousel-control-prev" type="button" data-bs-target="#personalRecentCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#personalRecentCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Suivant</span>
        </button>
      </div>
    </div>
  <?php else: ?>
    <p>Aucun livre récent dans votre sélection.</p>
  <?php endif; ?>

  <?php
    // Filtrer les livres à paraître pour la sélection personnelle
    $personalUpcoming = array_filter($upcomingBooks, function($book) use ($userSubscriptions) {
        if (!empty($book->labels) && is_array($book->labels)) {
            foreach ($book->labels as $labelId) {
                if (in_array($labelId, $userSubscriptions)) {
                    return true;
                }
            }
        }
        return false;
    });
    $chunksPersonalUpcoming = array_chunk($personalUpcoming, 6);
  ?>
  <h2>À paraître (sélection personnelle)</h2>
  <?php if (!empty($personalUpcoming)): ?>
    <div class="carousel-container">
      <div id="personalUpcomingCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php foreach ($chunksPersonalUpcoming as $index => $chunk): ?>
            <div class="carousel-item <?= ($index == 0 ? 'active' : '') ?>">
              <div class="row">
                <?php foreach ($chunk as $book): ?>
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
        <button class="carousel-control-prev" type="button" data-bs-target="#personalUpcomingCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#personalUpcomingCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Suivant</span>
        </button>
      </div>
    </div>
  <?php else: ?>
    <p>Aucun livre à paraître dans votre sélection.</p>
  <?php endif; ?>
<?php else: ?>
  <p>Vous devez être connecté pour voir votre sélection personnelle.</p>
<?php endif; ?>
