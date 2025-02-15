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
            
            <?php if (!empty($book['serieName'])): ?>
                <div class="row">
                    <div class="col-md-8">
                        <p><strong>Série :</strong> <?= esc($book['serieName']) ?></p>
                    </div>
                    <div class="col-md-4">
                        <?php if (!empty($book['volume'])): ?>
                            <p>
                                <strong>Tome :</strong>
                                <?php 
                                    $volume = $book['volume'];
                                    if ($volume === 'I') {
                                        echo "Intégrale";
                                    } elseif ($volume === 'HS') {
                                        echo "Hors-Série";
                                    } else {
                                        echo esc($volume);
                                    }
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Format :</strong> <?= esc($book['format']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>ISBN :</strong> <?= esc($book['isbn'] ?? 'Non renseigné') ?></p>
                </div>
            </div>
            
            <?php
                // Extraction des noms des genres et sous-genres
                $genreNames = [];
                if (!empty($book['genres'])) {
                    $genreNames = array_map(function($genre) {
                        return $genre['name'];
                    }, $book['genres']);
                }
                $subgenreNames = [];
                if (!empty($book['subgenres'])) {
                    $subgenreNames = array_map(function($subgenre) {
                        return $subgenre['name'];
                    }, $book['subgenres']);
                }
            ?>
            <?php if (!empty($genreNames)): ?>
                <p><strong>Genres :</strong> <?= implode(', ', $genreNames) ?></p>
            <?php endif; ?>
            
            <?php if (!empty($subgenreNames)): ?>
                <p><strong>Sous-genres :</strong> <?= implode(', ', $subgenreNames) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col">
            <h5>Résumé</h5>
            <p><?= esc($book['description'] ?? 'Non renseigné') ?></p>
        </div>
    </div>
    
    <?php if (!empty($labels)): ?>
        <div class="mt-3">
            <h5>Labels associés :</h5>
            <?php foreach ($labels as $label): ?>
                <?php 
                    // Détermine la couleur du badge selon le préfixe
                    $prefix = substr($label->id, 0, 2);
                    $colorClass = 'bg-secondary';
                    switch ($prefix) {
                        case 'AU':
                            $colorClass = 'bg-info';
                            break;
                        case 'PU':
                            $colorClass = 'bg-primary';
                            break;
                        case 'SE':
                            $colorClass = 'bg-warning';
                            break;
                        case 'GE':
                            $colorClass = 'bg-success';
                            break;
                        case 'SG':
                            $colorClass = 'bg-dark';
                            break;
                    }
                    // Applique la classe selon l'état de souscription enrichi côté serveur
                    $subscribedClass = $label->subscribed ? 'subscribed' : 'unsubscribed';
                ?>
                <span class="badge <?= $colorClass ?> text-light me-1 label-click <?= $subscribedClass ?>" data-label="<?= esc($label->id) ?>">
                    <?= esc($label->labelName) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript pour la gestion de la souscription sur les labels -->
<script>
$(document).ready(function(){
    $('.label-click').on('click', function(){
        var labelId = $(this).data('label');
        var badge = $(this);
        $.ajax({
            url: "<?= site_url('label/subscribeLabel') ?>",
            type: "POST",
            data: { label: labelId },
            success: function(response){
                alert(response.message);
                // Bascule les classes en fonction de la réponse
                if(response.action === 'subscribed'){
                    badge.removeClass('unsubscribed').addClass('subscribed');
                } else if(response.action === 'unsubscribed'){
                    badge.removeClass('subscribed').addClass('unsubscribed');
                }
            },
            error: function(xhr, status, error){
                alert("Erreur lors de la souscription. Veuillez réessayer.");
            }
        });
    });
});
</script>

<style>
.badge.subscribed {
    opacity: 1 !important; 
}
.badge.unsubscribed {
    opacity: 0.6 !important; 
}
.label-click {
    cursor: pointer;
}
</style>
