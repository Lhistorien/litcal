<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h1><?= $title ?></h1>

<?php if (isset($user) && !empty($user->name)): ?>
    <p>Bienvenue, <?= esc($user->name) ?> !</p>
<?php endif; ?>

<div class="row">
  <?php foreach ($books as $book) : ?>
    <?= view_cell('\App\Libraries\DisplayBook::displayBook', ['title' => $book]) ?>
  <?php endforeach; ?>
</div>

<?= $this->endSection() ?>