$(document).ready(function() {
    // Gestion du clic pour ouvrir le modal avec les détails du livre
    $(document).on('click', '.book-link', function(e) {
        e.preventDefault();
        var bookId = $(this).data('id');
        console.log("Clic sur un livre, ID:", bookId);
        
        $.ajax({
            url: site_url + '/book/details/' + bookId,  // Voir ci-dessous pour définir site_url
            type: 'GET',
            success: function(response) {
                console.log("Réponse AJAX :", response); 
                $('#bookDetailsModal .modal-body').html(response);
                // Stocke l'ID du livre dans le modal pour l'utiliser lors de la souscription
                $('#bookDetailsModal').data('book-id', bookId);
                $('#bookDetailsModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX :', error);
                $('#bookDetailsModal .modal-body').html('<p>Une erreur est survenue lors du chargement des détails.</p>');
                $('#bookDetailsModal').modal('show');
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
                console.log("Réponse AJAX (subscribe):", response);
                if(response.success) {
                    alert(response.message);
                    // Met à jour l'apparence du bouton selon l'action
                    if(response.action === 'follow') {
                        $('#subscribeBookBtn').removeClass('btn-success').addClass('btn-danger').text('Ne plus suivre');
                    } else if(response.action === 'unfollow') {
                        $('#subscribeBookBtn').removeClass('btn-danger').addClass('btn-success').text('Suivre ce livre');
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX lors de la souscription :', error);
                alert("Une erreur est survenue lors du suivi du livre.");
            }
        });
    });
});
console.log('bookActions.js loaded');
