<?php
include '../inc/connection.php';

$dept_no = $_GET['dept_no'] ?? '';
$page = $_GET['page'] ?? 0;
$offset = $page * 20;

// Requête pour obtenir les informations du département
$dept_query = "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'";
$dept_result = mysqli_query($dataBase, $dept_query);
$dept_info = mysqli_fetch_assoc($dept_result);

// Requête pour obtenir les employés du département avec pagination
$employees_query = "
    SELECT e.emp_no, e.first_name, e.last_name, e.hire_date
    FROM employees e
    JOIN dept_emp de ON e.emp_no = de.emp_no
    WHERE de.dept_no = '$dept_no' AND de.to_date > NOW()
    ORDER BY e.last_name, e.first_name
    LIMIT $offset, 20
";
$employees_result = mysqli_query($dataBase, $employees_query);

// Requête pour compter le nombre total d'employés
$count_query = "
    SELECT COUNT(*) as total 
    FROM employees e
    JOIN dept_emp de ON e.emp_no = de.emp_no
    WHERE de.dept_no = '$dept_no' AND de.to_date > NOW()
";
$count_result = mysqli_query($dataBase, $count_query);
$total_employees = mysqli_fetch_assoc($count_result)['total'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employés du département <?= htmlspecialchars($dept_info['dept_name'] ?? '') ?></title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <header class="mb-4 text-center">
            <h1 class="fw-bold">
                Employés du département : <?= htmlspecialchars($dept_info['dept_name'] ?? 'Inconnu') ?>
            </h1>
        </header>
        
        <main>
            <?php if (mysqli_num_rows($employees_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Numéro</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date d'embauche</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($employee = mysqli_fetch_assoc($employees_result)): ?>
                        <tr>
                            <td><?= $employee['emp_no'] ?></td>
                            <td><?= $employee['last_name'] ?></td>
                            <td><?= $employee['first_name'] ?></td>
                            <td><?= $employee['hire_date'] ?></td>
                            <td>
                                <form action="fiche_employe.php" method="get" class="d-inline">
                                    <input type="hidden" name="emp_no" value="<?= $employee['emp_no'] ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Voir fiche</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?php if ($page > 0): ?>
                <form action="employes_departement.php" method="get">
                    <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                    <input type="hidden" name="page" value="<?= $page - 1 ?>">
                    <button type="submit" class="btn btn-secondary">Précédent</button>
                </form>
                <?php else: ?><span></span><?php endif; ?>

                <?php if (($offset + 20) < $total_employees): ?>
                <form action="employes_departement.php" method="get">
                    <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                    <input type="hidden" name="page" value="<?= $page + 1 ?>">
                    <button type="submit" class="btn btn-secondary">Suivant</button>
                </form>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mt-4">Aucun employé trouvé dans ce département.</div>
            <?php endif; ?>
        </main>

        <footer class="mt-5 text-center">
            <form action="../index.php" method="get">
                <button type="submit" class="btn btn-dark">Retour à la liste des départements</button>
            </form>
        </footer>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>