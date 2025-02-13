<?php if(!empty($books)): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Date de sortie</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($books as $book): ?>
                <tr>
                    <td><?= esc($book->title) ?></td>
                    <td><?= esc($book->publication) ?></td>
                    <td><?= esc($book->roleName) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun livre trouvé pour cet auteur.</p>
<?php endif; ?>
