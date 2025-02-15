<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<h2>Livres qui vous intéressent</h2>

<?= $this->include('components/bookSubs') ?>

<h2>Vos abonnements</h2>

<?= $this->include('components/labelSubs') ?>

<?= $this->endSection() ?>
