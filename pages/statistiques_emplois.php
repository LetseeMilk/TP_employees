<?php
include '../inc/function.php';

$statistiques = getStatistiquesParEmploi($dataBase);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Statistiques par emploi</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <header class="mb-4 text-center">
            <h1 class="fw-bold">Statistiques des emplois</h1>
        </header>

        <main>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Emploi</th>
                            <th>Total employés</th>
                            <th>Hommes</th>
                            <th>Femmes</th>
                            <th>Salaire moyen </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($statistiques)): ?>
                        <?php foreach ($statistiques as $stat): ?>
                        <tr>
                            <td><?= $stat['title'] ?></td>
                            <td><?= $stat['total_employes'] ?></td>
                            <td><?= $stat['hommes'] ?></td>
                            <td><?= $stat['femmes'] ?></td>
                            <td><?= number_format($stat['salaire_moyen'], 2, ',', ' ') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucune donnée disponible.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="mt-4 text-center">
            <a href="../index.php" class="btn btn-dark">Retour à l'accueil</a>
        </footer>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>