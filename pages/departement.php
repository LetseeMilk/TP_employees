<?php
include '../inc/connection.php';

$query = "
    SELECT d.dept_no, d.dept_name, e.first_name, e.last_name
    FROM departments d
    LEFT JOIN dept_manager dm ON d.dept_no = dm.dept_no 
    LEFT JOIN employees e ON dm.emp_no = e.emp_no
    WHERE dm.to_date > NOW()
    ORDER BY d.dept_name
";

$result = mysqli_query($dataBase, $query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des départements</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <header class="mb-4 text-center">
            <h1 class="fw-bold">Liste des départements</h1>
        </header>
        
        <main>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Numéro</th>
                            <th scope="col">Nom du département</th>
                            <th scope="col">Manager actuel</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['dept_no'] ?></td>
                            <td><?= $row['dept_name'] ?></td>
                            <td><?= trim($row['first_name'] . ' ' . $row['last_name']) ?: 'Non défini' ?></td>
                            <td>
                                <form action="employes_departement.php" method="get" class="d-inline">
                                    <input type="hidden" name="dept_no" value="<?= $row['dept_no'] ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Voir les employés</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
        
        <footer class="mt-4 text-center">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <form action="recherche.php" method="get">
                    <button type="submit" class="btn btn-secondary">Recherche avancée</button>
                </form>
                <form action="../index.php" method="get">
                    <button type="submit" class="btn btn-dark">Retour à l'accueil</button>
                </form>
            </div>
        </footer>
    </div>

        <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>