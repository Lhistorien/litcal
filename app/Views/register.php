<?= $this->extend('Layouts/main_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h2 class="text-center mb-4">Créer un compte</h2>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
                        
        <form id="registerForm" method="post" action="<?= site_url('/register') ?>">
            <div class="mb-3">
                <label for="inputPseudo" class="form-label">Pseudonyme</label>
                <input name="pseudo" type="text" class="form-control" id="inputPseudo" required>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label">Email</label>
                <input name="email" type="email" class="form-control" id="inputEmail" required>
            </div>
            <div class="mb-3">
                <label for="inputBirthday" class="form-label">Date de naissance</label>
                <input name="birthday" type="date" class="form-control" id="inputBirthday" required>
            </div>
            <div class="mb-3">
                <label for="inputPassword" class="form-label">Mot de passe</label>
                <input name="password" type="password" class="form-control" id="inputPassword" required>
            </div>
            <div class="mb-3">
                <label for="inputPasswordControl" class="form-label">Confirmer le mot de passe</label>
                <input name="pwdcontrol" type="password" class="form-control" id="inputPasswordControl" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#registerForm").submit(function(event) 
    {
        let isValid = true;
        let errors = [];

        let pseudo = $("#inputPseudo").val().trim();
        let email = $("#inputEmail").val().trim();
        let birthday = $("#inputBirthday").val();
        let password = $("#inputPassword").val();
        let pwdControl = $("#inputPasswordControl").val();

        if (pseudo.length < 4 || pseudo.length > 30) {
            isValid = false;
            errors.push("Le pseudo doit contenir entre 4 et 30 caractères.");
        }
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            isValid = false;
            errors.push("Veuillez entrer une adresse e-mail valide.");
        }
        if (!birthday) {
            isValid = false;
            errors.push("Veuillez entrer une date de naissance valide.");
        }
        let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,255}$/;
        if (!passwordRegex.test(password)) {
            isValid = false;
            errors.push("Le mot de passe doit contenir au moins 8 caractères, une minuscule, une majuscule et un chiffre.");
        }
        if (password !== pwdControl) {
            isValid = false;
            errors.push("Le mot de passe n\'a pas été répété correctement.");
        }
        // Empêche le formulaire de s'envoyer s'il y a au moins une erreur. Collecte la liste des erreurs et les affiche en haut de la "card"
        if (!isValid) {
            event.preventDefault(); 

            let errorHtml = '<div class="alert alert-danger"><ul>';
            errors.forEach(error => {
                errorHtml += `<li>${error}</li>`;
            });
            errorHtml += '</ul></div>';

            $(".card").prepend(errorHtml);
        }
    });
});
</script>

<?= $this->endSection() ?>