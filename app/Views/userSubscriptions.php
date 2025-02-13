<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Mes abonnements</h1>

<?php if (!empty($subscriptions)): ?>
  <div class="table-responsive">
      <table class="table table-striped">
          <thead>
              <tr>
                  <th>Couverture</th>
                  <th>Titre</th>
                  <th>Date de publication</th>
                  <th>Éditeur</th>
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
                      <td><?= date('d/m/Y', strtotime($sub->publication)) ?></td>
                      <td><?= esc($sub->publisherName) ?></td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
  </div>
<?php else: ?>
  <p>Vous n'êtes abonné à aucun livre.</p>
<?php endif; ?>

<?= $this->endSection() ?>
