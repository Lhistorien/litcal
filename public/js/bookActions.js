$(document).ready(function() {
    // Gestion du clic pour ouvrir le modal avec les détails du livre
    $(document).on('click', '.book-link', function(e) {
        e.preventDefault();
        var bookId = $(this).data('id');
        var modalEl = document.getElementById('bookDetailsModal');
        var myModal = new bootstrap.Modal(modalEl);
        
        // Réinitialiser le bouton de suivi à son état par défaut
        $('#subscribeBookBtn')
            .removeClass('btn-danger')
            .addClass('btn-success')
            .text('Suivre ce livre');

        myModal.show();
        
        // Charger le contenu du modal via AJAX
        $.ajax({
            url: site_url + '/book/details/' + bookId,
            type: 'GET',
            success: function(response) {
                $('#bookDetailsModal .modal-body').html(response);
                // Stocke l'ID du livre pour d'autres actions (abonnement, désactivation, etc.)
                $('#bookDetailsModal').data('book-id', bookId);
            },
            error: function(xhr, status, error) {
                $('#bookDetailsModal .modal-body').html('<p>Une erreur est survenue lors du chargement des détails.</p>');
            }
        });
    });
    
    // Gestion du clic sur le bouton "Suivre ce livre"
    $(document).on('click', '#subscribeBookBtn', function(e) {
        e.preventDefault();
        var bookId = $('#bookDetailsModal').data('book-id');
        if (!bookId) {
            alert("Livre non identifié.");
            return;
        }
        
        $.ajax({
            url: site_url + '/book/subscribe/' + bookId,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    if ($.trim(response.action) === 'follow') {
                        $('#subscribeBookBtn').removeClass('btn-success').addClass('btn-danger').text('Ne plus suivre');
                    } else if ($.trim(response.action) === 'unfollow') {
                        $('#subscribeBookBtn').removeClass('btn-danger').addClass('btn-success').text('Suivre ce livre');
                    } else {
                        $('#subscribeBookBtn').text('Action: ' + $.trim(response.action));
                    }
                } else {
                    alert(response.message);
                }                
            },
            error: function(xhr, status, error) {
                alert("Une erreur est survenue lors du suivi du livre.");
            }
        });
    });
    
    // Gestion du clic sur le bouton "Désactiver ce livre"
    $(document).on('click', '#deactivateBookBtn', function(e) {
        e.preventDefault();
        var bookId = $('#bookDetailsModal').data('book-id');
        if (!bookId) {
            alert("Livre non identifié.");
            return;
        }
        
        $.ajax({
            url: site_url + '/book/deactivate/' + bookId, 
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#bookDetailsModal').modal('hide');
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert("Une erreur est survenue lors de la désactivation du livre.");
            }
        });
    });
});
