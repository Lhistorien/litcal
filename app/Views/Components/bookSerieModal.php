<?php if(!empty($books)): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Tome</th>
                <th>Date de sortie</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($books as $book): ?>
                <tr>
                    <td><?= esc($book->title) ?></td>
                    <td><?= esc($book->volume) ?></td>
                    <td><?= esc(date('d/m/y', strtotime($book->publication))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun livre trouvé pour cette série.</p>
<?php endif; ?>
