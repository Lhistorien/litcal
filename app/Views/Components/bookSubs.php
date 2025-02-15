<?php if (!empty($subscriptions)): ?>
  <div class="table-responsive">
      <table class="table table-striped">
          <thead>
              <tr>
                  <th>Couverture</th>
                  <th>Titre</th>
                  <th>Auteur(s)</th>
                  <th>Date de sortie</th>
                  <th>Éditeur</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($subscriptions as $sub): ?>
                  <tr>
                      <td>
                          <img src="<?= base_url(!empty($sub->cover) ? $sub->cover : 'cover/defaultcover.jpg') ?>" 
                               alt="Couverture de <?= esc($sub->title) ?>" width="80">
                      </td>
                      <td><?= esc($sub->title) ?></td>
                      <td><?= esc($sub->authors) ?></td>
                      <td><?= date('d/m/Y', strtotime($sub->publication)) ?></td>
                      <td><?= esc($sub->publisherName) ?></td>
                      <td>
                          <form action="<?= base_url('user/unsubscribe/' . $sub->subscriptionId) ?>" method="post">
                              <?= csrf_field() ?>
                              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous retirer ce livre de votre liste d\'envie ?')">
                                  Supprimer
                              </button>
                          </form>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
  </div>
<?php else: ?>
  <p>Vous n'êtes abonné à aucun livre.</p>
<?php endif; ?>


