<br>
<h3>Tableau des Labels</h3>

<table id="labelsTable" class="table table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Label</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($labels as $label): ?>
            <tr>
                <td><?= esc($label->id) ?></td>
                <td><?= esc($label->labelName) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
