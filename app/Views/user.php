<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1 class="text-center"><?= $title ?></h1>
<br>
<?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger">
        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
            <p><?= esc($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success">
        <p><?= session()->getFlashdata('success') ?></p>
    </div>
<?php endif; ?>
<div class="container d-flex justify-content-center">
    <div class="card shadow-sm p-4 w-auto position-relative">
        <button class="btn btn-sm btn-warning position-absolute top-0 end-0 m-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            <i class="bi bi-pencil"></i> <b>Modifier</b>
        </button>

        <div class="card-body mt-3">
            <ul class="list-group list-group-flush w-auto">
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-person"></i> <b>Pseudonyme</b> :</span> 
                    <span><?= $user->pseudo ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-envelope"></i> <b>Email</b> :</span> 
                    <span><?= $user->email ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span><i class="bi bi-calendar"></i> <b>Date de naissance</b> :&nbsp;</span>
                    <span><?= date('d/m/Y', strtotime($user->birthday)) ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal de modification du profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Modifier vos informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= site_url('user/update/' . $user->id) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudonyme</label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?= $user->pseudo ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="profile-email" name="email" value="<?= $user->email ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" value="<?= $user->birthday ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="OldPassword" class="form-label">Ancien mot de passe (laisser vide si inchangé)</label>
                        <input type="password" class="form-control" id="OldPassword" name="OldPassword">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Nouveau mot de passe (laisser vide si inchangé)</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                    </div>
                    <div class="mb-3">
                        <label for="pwdControl" class="form-label">Veuillez répéter le mot de passe (laisser vide si inchangé)</label>
                        <input type="password" class="form-control" id="pwdControl" name="pwdControl">
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/js/user-edit.js"></script>

<?= $this->endSection() ?>
