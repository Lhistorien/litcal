<?= $this-> extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<table id="usersTable" class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Pseudo</th>
            <th scope="col">Email</th>
            <th scope="col">Birthday</th>
            <th scope="col">Rôle</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= esc($user->pseudo) ?></td>
                <td><?= esc($user->email) ?></td>
                <td><?= esc($user->birthday) ?></td>
                <td><?= esc($user->role) ?></td>
                <td>
                    <a href="/user/<?= esc($user->id) ?>" class="btn btn-primary btn-sm">Profil</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?= $this->endSection() ?>