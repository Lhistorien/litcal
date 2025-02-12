<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <img src="<?= base_url($book['cover']) ?>" alt="Couverture du livre <?= esc($book['title']) ?>" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-9">
            <div class="mb-3">
                <?php if (in_array(session()->get('user_role'), ['Administrator', 'Contributor'])): ?>
                    <!-- Modifier remplacé par un lien -->
                    <a href="<?= site_url('book/edit/' . $book['id']) ?>" class="btn btn-primary">Modifier</a>
                <?php endif; ?>
            </div>

            <ul class="list-group">
                <li class="list-group-item">
                    <strong><?= count($book['authors']) > 1 ? 'Auteurs' : 'Auteur' ?> :</strong>
                    <?php foreach ($book['authors'] as $author): ?>
                        <?= esc($author['name']) ?>
                        <?php if ($author['role'] !== 'Auteur'): ?>
                            (<?= esc($author['role']) ?>)
                        <?php endif; ?>
                        <?php if ($author !== end($book['authors'])): ?>
                            ,
                        <?php endif; ?>
                    <?php endforeach; ?>
                </li>

                <?php if (!empty($book['serieName'])) : ?>
                    <li class="list-group-item">
                        <strong>Série :</strong> <?= esc($book['serieName']) ?> - <strong>Tome :</strong> <?= esc($book['volume']) ?>
                    </li>
                <?php endif; ?>

                <li class="list-group-item">
                    <div class="row gx-3">
                        <div class="col-md-6 border-end">
                            <strong>Éditeur :</strong> <?= esc($book['publisherName']) ?>
                            <?php if (!empty($book['link'])) : ?>
                                <a href="<?= esc($book['link']) ?>" target="_blank" class="ms-2"><i class="fas fa-external-link-alt"></i></a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Langue :</strong> <?= esc($book['languageName']) ?>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row gx-3">
                        <div class="col-md-6 border-end">
                            <strong>Date de publication :</strong> <?= esc(date('d/m/Y', strtotime($book['publication']))) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Prix :</strong> <?= esc($book['price'] ?? 'Non renseigné') ?> €
                            <?= $book['preorder'] ? '<span class="text-warning">(précommande)</span>' : '' ?>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row gx-3">
                        <div class="col-md-6 border-end">
                            <strong>ISBN :</strong> <?= esc($book['isbn'] ?? 'Non renseigné') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Format :</strong> <?= esc($book['format']) ?>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <div class="row gx-3">
                        <div class="col-md-6 border-end">
                            <strong>Genres :</strong>
                            <?php foreach ($book['genres'] as $genre) : ?>
                                <?= esc($genre) ?>
                                <?php if ($genre !== end($book['genres'])): ?>
                                    ,
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Sous-genres :</strong>
                            <?php foreach ($book['subgenres'] as $subgenre) : ?>
                                <?= esc($subgenre) ?>
                                <?php if ($subgenre !== end($book['subgenres'])): ?>
                                    ,
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <strong>Résumé :</strong> <?= esc($book['description'] ?? 'Non renseigné') ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
