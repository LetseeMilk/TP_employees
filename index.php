<?php
include 'inc/function.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des départements</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <header class="mb-4 text-center">
            <h1 class="fw-bold">Liste des départements</h1>
        </header>
        
<main>

    <?php
    $departements = afficherDepartements($dataBase);

    if (empty($departements)) {
        echo '<p class="text-danger">Erreur de chargement des départements.</p>';
    } else {
    ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Numéro</th>
                        <th>Nom du département</th>
                        <th>Manager actuel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($departements as $row) {
                    $manager = trim($row['first_name'] . ' ' . $row['last_name']);
                    if ($manager === '') $manager = 'Non défini';
                ?>
                    <tr>
                        <td><?php echo ($row['dept_no']); ?></td>
                        <td><?php echo ($row['dept_name']); ?></td>
                        <td><?php echo ($manager); ?></td>
                        <td>
                            <form action="pages/employes_departement.php" method="get" class="d-inline">
                                <input type="hidden" name="dept_no" value="<?php echo ($row['dept_no']); ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Voir les employés</button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    <?php
    }
    ?>
</main>

        
        <footer class="mt-4 text-center">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <form action="pages/recherche.php" method="get">
                    <button type="submit" class="btn btn-secondary">Recherche avancée</button>
                </form>
            </div>
        </footer>
    </div>

        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>