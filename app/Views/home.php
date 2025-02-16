<?= $this->extend('Layouts/main_layout') ?>
<?= $this->section('content') ?>

<h1>Accueil</h1>

<ul class="nav nav-tabs" id="homeTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
      Toutes les sorties récentes et à venir
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="false">
      Votre sélection personnalisée
    </button>
  </li>
</ul>

<div class="tab-content" id="homeTabsContent">
  <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
    <?= $this->include('Components/allBooksCarousels') ?>
  </div>
  <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
    <?= $this->include('Components/personalBooksCarousels') ?>
  </div>
</div>

<!-- Ajoutez ici le markup du modal, en dehors des onglets -->
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

<script>
    $(document).ready(function(){
        // Initialisation de tous les carousels présents dans la page
        $('#recentCarousel, #upcomingCarousel, #personalRecentCarousel, #personalUpcomingCarousel').carousel();
    });
</script>
<script>
  $(document).ready(function(){
      $('#bookDetailsModal').appendTo('body');
  });
  $(document).on('click', '.book-link', function(e) {
    e.preventDefault();
    var bookId = $(this).data('id');
    // Met à jour le lien du bouton "Modifier" dans le modal
    $('#editBookBtn').attr('href', "<?= site_url('book/edit') ?>/" + bookId);
    $('#bookDetailsModal').modal('show');
});

</script>


<?= $this->endSection() ?>
