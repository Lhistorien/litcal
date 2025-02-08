<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $meta_title ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('public/css/styles.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  </head>

  <body>
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">LitCal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <?php if (session()->get('is_logged_in') && (session()->get('user_role') === 'Administrator' || session()->get('user_role') === 'Contributor')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="bddDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                BDD
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="bddDropdown">
                                <li><a class="dropdown-item" href="/books">Livres</a></li>
                                <li><a class="dropdown-item" href="/authors">Auteurs</a></li>
                                <li><a class="dropdown-item" href="/series">Série</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">Dashboard</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex me-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                <div class="d-flex align-items-center">
                    <?php if (session()->get('is_logged_in')): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Profil
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/user/<?= session()->get('user_id') ?>">Votre compte</a></li>
                                <li><a class="dropdown-item" href="#">Vos abonnements</a></li>
                                <li>
                                    <form action="/logout" method="post" style="margin: 0;">
                                        <button type="submit" class="dropdown-item">Se déconnecter</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


    <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" id="danger-alert">
      <?php if (is_array($errors)): ?>
        <?php foreach ($errors as $error): ?>
          <p><?= esc($error) ?></p>
        <?php endforeach; ?>
      <?php else: ?>
        <p><?= esc($errors) ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" id="success-alert">
      <p><?= esc(session()->getFlashdata('success')) ?></p>
    </div>
  <?php endif; ?>
  <!-- Utilisation d'un cookie pour afficher le message de déconnection puisque la session a été destroy -->
  <?php if (isset($_COOKIE['flash_success'])): ?> 
    <div class="alert alert-success" id="success-alert"><?= esc($_COOKIE['flash_success']) ?></div>
    <?php setcookie("flash_success", "", time() - 3600, "/"); ?>
  <?php endif; ?>


    <div class="container">
      <?= $this -> renderSection('content')?>  
    </div>
    <!-- Modal servant à se connecter -->
    <div class="modal" tabindex="-1" id="loginModal">
      <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Connexion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="loginForm" action="/auth" method="post">
                  <div class="mb-3">
                      <label for="email" class="form-label">Adresse Email</label>
                      <input type="email" name="email" class="form-control" id="email" required>
                  </div>
                  <div class="mb-3">
                      <label for="password" class="form-label">Mot de passe</label>
                      <input type="password" name="password" class="form-control" id="password" required>
                  </div>
                  <div class="modal-footer">
                      <a href="/register" class="btn btn-secondary">Créer un compte</a>
                      <button type="submit" class="btn btn-primary">Se connecter</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
    </div>

   <script>
    $(document).ready(function () 
    {
      // JS pour afficher les alertes
      $("#success-alert")
        .fadeTo(2000, 500)
        .slideUp(500, () => {
          $(this).remove(); 
        });
      $("#danger-alert").fadeTo(2000, 500).slideUp(500, () => {
        $(this).remove(); 
      });
      // AJAX pour le login dynamique
        // $("#loginForm").submit(function (event) 
        // {
        //     event.preventDefault(); 
        //     $.ajax
        //     (
        //       {
        //         url: "/auth",
        //         type: "POST",
        //         data: $(this).serialize(), 
        //         dataType: "json",
        //         success: function (response) 
        //         {
        //             if (response.success) 
        //             {
        //                 $("#loginModal").modal("hide");
        //                 $("#success-alert").html(`<p> ${response.message}!</p>`).fadeIn();

        //                 $(".d-flex.align-items-center").html
        //                 (`
        //                     <div class="dropdown">
        //                         <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        //                             Profil
        //                         </button>
        //                         <ul class="dropdown-menu dropdown-menu-end">
        //                             <li><a class="dropdown-item" href="/user/${response.user_id}">Votre compte</a></li>
        //                             <li><a class="dropdown-item" href="#">Vos abonnements</a></li>
        //                             <li>
        //                                 <form action="/logout" method="get" style="margin: 0;">
        //                                     <button type="submit" class="dropdown-item">Se déconnecter</button>
        //                                 </form>
        //                             </li>
        //                         </ul>
        //                     </div>
        //                 `);
        //             } 
        //             else 
        //             {
        //                 $("#danger-alert").html(`<p>${response.error}</p>`).fadeIn();
        //             }
        //         },
        //         error: function () 
        //         {
        //             $("#danger-alert").html("<p>Une erreur est survenue, veuillez réessayer.</p>").fadeIn();
        //         }
        //     }
        //   );
        // }
      // );
    }
  );
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>