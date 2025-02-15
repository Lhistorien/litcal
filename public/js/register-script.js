    // Script de validation côté client pour plus de sécurité
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