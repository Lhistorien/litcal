<br>
<h3>Tableau des utilisateurs</h3>

<table id="usersTable" class="table table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Pseudo</th>
            <th scope="col">Email</th>
            <th scope="col">Birthday</th>
            <th scope="col">Rôle</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr data-id="<?= esc($user->id) ?>">
                <td><?= esc($user->id) ?></td>
                <td class="editable" data-field="pseudo"><?= esc($user->pseudo) ?></td>
                <td class="editable" data-field="email"><?= esc($user->email) ?></td>
                <td class="editable" data-field="birthday"><?= esc($user->birthday) ?></td>
                <td class="editable" data-field="role"><?= esc($user->role) ?></td>
                <td class="editable" data-field="status"><?= esc($user->status) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="/js/user-edit.js"></script>
