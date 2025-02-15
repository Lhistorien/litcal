<?php if (!empty($labelSubscriptions)): ?>
  <div class="table-responsive">
      <table class="table table-striped">
          <thead>
              <tr>
                  <th>Label</th>
                  <th>Type</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($labelSubscriptions as $sub): ?>
                  <?php 
                      // Ici, $sub->label contient l'identifiant du label (par exemple "AU27")
                      $prefix = substr($sub->label, 0, 2);
                      switch ($prefix) {
                          case 'AU':
                              $type = 'Auteur';
                              $colorClass = 'bg-info';
                              break;
                          case 'PU':
                              $type = 'Éditeur';
                              $colorClass = 'bg-primary';
                              break;
                          case 'SE':
                              $type = 'Série';
                              $colorClass = 'bg-warning';
                              break;
                          case 'GE':
                              $type = 'Genre';
                              $colorClass = 'bg-success';
                              break;
                          case 'SG':
                              $type = 'Sous-genre';
                              $colorClass = 'bg-dark';
                              break;
                          default:
                              $type = 'Label';
                              $colorClass = 'bg-secondary';
                      }
                      // Utiliser la propriété "subscribed" pour déterminer la classe d'état
                      $subscribedClass = $sub->subscribed ? 'subscribed' : 'unsubscribed';
                  ?>
                  <tr>
                      <td>
                          <span class="badge <?= $colorClass ?> text-light me-1 label-click <?= $subscribedClass ?>" data-label="<?= esc($sub->label) ?>">
                              <?= esc($sub->labelName) ?>
                          </span>
                      </td>
                      <td><?= esc($type) ?></td>
                      <td>
                          <form action="<?= base_url('user/unsubscribeLabel/' . $sub->subscriptionId) ?>" method="post">
                              <?= csrf_field() ?>
                              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vous désabonner de ce label ?')">
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
  <p>Vous n'êtes abonné à aucun label.</p>
<?php endif; ?>
