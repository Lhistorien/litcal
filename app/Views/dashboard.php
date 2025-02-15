<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1>Bienvenue sur le Dashboard</h1>
<!-- Centralise la gestion des tables par les admins et les contributeurs -->
<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
    <?php 
    $allowedRoles = ['Administrator', 'Contributor'];
    if (in_array(session()->get('user_role'), $allowedRoles)): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="authors-tab" data-bs-toggle="tab" data-bs-target="#authors" type="button" role="tab">Auteurs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="series-tab" data-bs-toggle="tab" data-bs-target="#series" type="button" role="tab">Séries</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="publishers-tab" data-bs-toggle="tab" data-bs-target="#publishers" type="button" role="tab">Éditeurs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="languages-tab" data-bs-toggle="tab" data-bs-target="#languages" type="button" role="tab">Langues</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">Rôles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="genres-tab" data-bs-toggle="tab" data-bs-target="#genres" type="button" role="tab">Genres</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="subgenres-tab" data-bs-toggle="tab" data-bs-target="#subgenres" type="button" role="tab">Sous-genres</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="labels-tab" data-bs-toggle="tab" data-bs-target="#labels" type="button" role="tab">Labels</button>
        </li>
    <?php endif; ?>
    <!-- L'onglet users est réservé aux admins -->
    <?php if (session()->get('user_role') === 'Administrator'): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Utilisateurs</button>
        </li>
    <?php endif; ?>
</ul>

<div class="tab-content" id="dashboardTabsContent">


    <?php if (in_array(session()->get('user_role'), $allowedRoles)): ?>
        <div class="tab-pane fade" id="authors" role="tabpanel">
            <?= $this->include('components/authors_table') ?>
        </div>
        <div class="tab-pane fade" id="series" role="tabpanel">
            <?= $this->include('components/series_table') ?>
        </div>
        <div class="tab-pane fade" id="publishers" role="tabpanel">
            <?= $this->include('components/publishers_table') ?>
        </div>
        <div class="tab-pane fade" id="languages" role="tabpanel">
            <?= $this->include('components/languages_table') ?>
        </div>
        <div class="tab-pane fade" id="roles" role="tabpanel">
            <?= $this->include('components/roles_table') ?>
        </div>
        <div class="tab-pane fade" id="genres" role="tabpanel">
            <?= $this->include('components/genres_table') ?>
        </div>
        <div class="tab-pane fade" id="subgenres" role="tabpanel">
            <?= $this->include('components/subgenres_table') ?>
        </div>
        <div class="tab-pane fade" id="labels" role="tabpanel">
            <?= $this->include('components/labels_table') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->get('user_role') === 'Administrator'): ?>
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <?= $this->include('components/users_table') ?>
        </div>
    <?php endif; ?>
</div>

<!-- Script permetant de rester sur le bon onglet après une insertion de données, hash = l'id de l'onglet -->
<script>
    $(document).ready(function () {
        var hash = window.location.hash;
        if (hash) {
            var tabTrigger = new bootstrap.Tab(document.querySelector('button[data-bs-target="' + hash + '"]'));
            tabTrigger.show();
        }
    });
</script>

<?= $this->endSection() ?>
