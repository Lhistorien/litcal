<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue dans l'espace Séries</h1>

<div class="table-responsive">
    <table id="seriesTable" class="table table-striped">
        <thead>
            <tr>
                <th>Séries</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalSeries = count($series);
            for ($i = 0; $i < $totalSeries; $i += 2): 
            ?>
            <tr>
                <td>
                    <a href="#" class="series-link" data-id="<?= esc($series[$i]->id) ?>">
                        <?= esc($series[$i]->serieName) ?>
                    </a>
                </td>
                <td>
                    <?= isset($series[$i+1]) ? '<a href="#" class="series-link" data-id="'.esc($series[$i+1]->id).'">'.esc($series[$i+1]->serieName).'</a>' : '' ?>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour afficher les livres liés à une série -->
<div class="modal fade" id="seriesBooksModal" tabindex="-1" aria-labelledby="seriesBooksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seriesBooksModalLabel">Livres liés à la série</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Chargement...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="subscribeSerieBtn">S'abonner à la série</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Traduit la DataTable en français
    $('#seriesTable').DataTable({
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
    
    var currentSeriesId = null;
    
    $('#seriesTable tbody').on('click', '.series-link', function(e) {
        e.preventDefault();
        currentSeriesId = $(this).data('id');
        var seriesName = $(this).text();
        
        // Mise à jour du titre du modal avec le nom de la série
        $('#seriesBooksModalLabel').text("Livres liés à " + seriesName);
        
        $.ajax({
            url: '/serie/details/' + currentSeriesId,
            type: 'POST',
            success: function(response) {
                $('#seriesBooksModal .modal-body').html(response);
                
                $.ajax({
                    url: '<?= site_url("checkSerieSubscription") ?>',
                    type: 'POST',
                    data: { serieId: currentSeriesId },
                    success: function(res) {
                        if (res.subscribed) {
                            $("#subscribeSerieBtn")
                                .text("Se désabonner")
                                .removeClass('btn-primary')
                                .addClass('btn-danger');
                        } else {
                            $("#subscribeSerieBtn")
                                .text("S'abonner à la série")
                                .removeClass('btn-danger')
                                .addClass('btn-primary');
                        }
                    },
                    error: function() {
                        console.error("Erreur lors de la vérification de la souscription.");
                    }
                });
                
                $('#seriesBooksModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', error);
            }
        });
    });
    
    $('#subscribeSerieBtn').click(function() {
        if (!currentSeriesId) {
            alert('Aucune série sélectionnée.');
            return;
        }
        $.ajax({
            url: '<?= site_url("subscribeSerieLabel") ?>',
            type: 'POST',
            data: { serieId: currentSeriesId },
            success: function(response) {
                alert(response.message);
                if (response.action === 'subscribed') {
                    $("#subscribeSerieBtn")
                        .text("Se désabonner")
                        .removeClass('btn-primary')
                        .addClass('btn-danger');
                } else if (response.action === 'unsubscribed') {
                    $("#subscribeSerieBtn")
                        .text("S'abonner à la série")
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
