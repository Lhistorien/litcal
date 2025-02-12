<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<form method="post" action="<?= site_url('book/updateBook') ?>">
    <input type="text" name="id" value="<?= esc($book['id']) ?>">

    <div>
        <label for="title">Titre</label>
        <input type="text" name="title" value="<?= esc($book['title']) ?>">
    </div>

    <div>
        <label for="publisher">Éditeur</label>
        <input type="text" name="publisherName" value="<?= esc($book['publisherName']) ?>">
    </div>

    <div>
        <label for="isbn">ISBN</label>
        <input type="text" name="isbn" value="<?= esc($book['isbn']) ?>">
    </div>

    <div>
        <label for="price">Prix</label>
        <input type="text" name="price" value="<?= esc($book['price']) ?>">
    </div>

    <div>
        <label for="language">Langue</label>
        <input type="text" name="languageAbbreviation" value="<?= esc($book['languageAbbreviation']) ?>">
    </div>

    <button type="submit">Enregistrer les modifications</button>
</form>

<?= $this->endSection() ?>