<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'espace Auteurs</h1>

<div class="table-container">
    <table id="authorsTable" class="table table-striped">
        <thead>
            <tr>
                <th>Auteurs</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <!-- Permet d'afficher 2 auteurs par ligne -->
            <?php 
            $totalAuthors = count($authors);
            for ($i = 0; $i < $totalAuthors; $i += 2): 
            ?>
            <tr>
                <td>
                    <a href="#" class="author-link" data-id="<?= esc($authors[$i]->id) ?>">
                        <?= esc($authors[$i]->authorName) ?>
                    </a>
                </td>
                <td>
                    <?= isset($authors[$i+1]) ? '<a href="#" class="author-link" data-id="'.esc($authors[$i+1]->id).'">'.esc($authors[$i+1]->authorName).'</a>' : '' ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour afficher le profil d'un auteur -->
<div class="modal fade" id="booksModal" tabindex="-1" aria-labelledby="booksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="booksModalLabel">Livres liés à l'auteur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Chargement...</p>
            </div>
            <div class="modal-footer">
                <!-- Bouton pour se désabonner / s'abonner -->
                <button type="button" class="btn btn-primary" id="subscribeAuthorBtn">S'abonner à l'auteur</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var table = $('#authorsTable').DataTable({
        "autoWidth": true,
        "responsive": true
    });

    // Variables pour stocker l'ID de l'auteur cliqué et l'ID du label associé
    var currentAuthorId = null;
    var currentLabelId = null; // label = "AU" + currentAuthorId


    $('#authorsTable tbody').on('click', '.author-link', function (e) {
    e.preventDefault(); 
    currentAuthorId = $(this).data('id');
    currentLabelId = 'AU' + currentAuthorId;


    var authorName = $(this).text();

    $('#booksModalLabel').text("Livres liés à " + authorName);

    // Charger les livres liés à l'auteur
    $.ajax({
        url: '/getAuthorBooks',
        type: 'POST',
        data: { id: currentAuthorId },
        success: function(response) {
            $('#booksModal .modal-body').html(response);
            // Vérifier la souscription à cet auteur
            $.ajax({
                url: "<?= site_url('checkAuthorSubscription') ?>",
                type: 'POST',
                data: { label: currentLabelId },
                success: function(res) {
                    if (res.subscribed) {
                        $("#subscribeAuthorBtn")
                            .text("Se désabonner")
                            .removeClass('btn-primary')
                            .addClass('btn-danger');
                    } else {
                        $("#subscribeAuthorBtn")
                            .text("S'abonner à l'auteur")
                            .removeClass('btn-danger')
                            .addClass('btn-primary');
                    }
                },
                error: function() {
                    console.error("Erreur lors de la vérification de la souscription.");
                }
            });
            $('#booksModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX: ', error);
        }
    });
});

    // Manipule le bouton de souscription
    $('#subscribeAuthorBtn').click(function () {
        if (!currentLabelId) {
            alert('Aucun auteur sélectionné.');
            return;
        }
        $.ajax({
            url: "<?= site_url('subscribeAuthorLabel') ?>",
            type: 'POST',
            data: { label: currentLabelId },
            success: function(response) {
                console.log('Réponse toggle:', response);
                alert(response.message);
                // Metà jour le texte et la classe du bouton selon l'action retournée
                if (response.action === 'subscribed') {
                    $("#subscribeAuthorBtn")
                        .text("Se désabonner")
                        .removeClass('btn-primary')
                        .addClass('btn-danger');
                } else if (response.action === 'unsubscribed') {
                    $("#subscribeAuthorBtn")
                        .text("S'abonner à l'auteur")
                        .removeClass('btn-danger')
                        .addClass('btn-primary');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la souscription:', error);
                alert("Erreur lors de la souscription. Veuillez réessayer.");
            }
        });
    });
});

</script>

<?= $this->endSection() ?>
