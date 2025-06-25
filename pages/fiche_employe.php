<?php
include '../inc/connection.php';

$emp_no = $_GET['emp_no'] ?? '';

$employee_query = "SELECT * FROM employees WHERE emp_no = $emp_no";
$employee_result = mysqli_query($dataBase, $employee_query);
$employee = mysqli_fetch_assoc($employee_result);

$salaries_query = "SELECT * FROM salaries WHERE emp_no = $emp_no ORDER BY from_date DESC";
$salaries_result = mysqli_query($dataBase, $salaries_query);


$titles_query = "SELECT * FROM titles WHERE emp_no = $emp_no ORDER BY from_date DESC";
$titles_result = mysqli_query($dataBase, $titles_query);

$current_dept_query = "
    SELECT d.dept_no, d.dept_name 
    FROM departments d
    JOIN dept_emp de ON d.dept_no = de.dept_no
    WHERE de.emp_no = $emp_no AND de.to_date > NOW()
";
$current_dept_result = mysqli_query($dataBase, $current_dept_query);
$current_dept = mysqli_fetch_assoc($current_dept_result);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche employé <?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></title>
   <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <header class="text-center mb-4">
            <h1 class="fw-bold">Fiche employé</h1>
        </header>

        <main>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Informations personnelles</div>
                <div class="card-body">
                    <p><strong>Numéro :</strong> <?= $employee['emp_no'] ?></p>
                    <p><strong>Nom :</strong> <?= $employee['last_name'] ?></p>
                    <p><strong>Prénom :</strong> <?= $employee['first_name'] ?></p>
                    <p><strong>Date de naissance :</strong> <?= $employee['birth_date'] ?></p>
                    <p><strong>Genre :</strong> <?= $employee['gender'] ?></p>
                    <p><strong>Date d'embauche :</strong> <?= $employee['hire_date'] ?></p>
                    <?php if ($current_dept): ?>
                    <p><strong>Département actuel :</strong>
                        <form action="employes_departement.php" method="get" class="d-inline">
                            <input type="hidden" name="dept_no" value="<?= $current_dept['dept_no'] ?>">
                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline"><?= $current_dept['dept_name'] ?></button>
                        </form>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">Historique des salaires</div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($salaries_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Salaire</th>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($salary = mysqli_fetch_assoc($salaries_result)): ?>
                                <tr>
                                    <td><?= $salary['salary'] ?> $</td>
                                    <td><?= $salary['from_date'] ?></td>
                                    <td><?= $salary['to_date'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Aucun historique de salaire trouvé.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">Historique des postes</div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($titles_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Date de début</th>
                                    <th>Date de fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($title = mysqli_fetch_assoc($titles_result)): ?>
                                <tr>
                                    <td><?= $title['title'] ?></td>
                                    <td><?= $title['from_date'] ?></td>
                                    <td><?= $title['to_date'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">Aucun historique de poste trouvé.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer class="text-center mt-4">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <?php if ($current_dept): ?>
                <form action="employes_departement.php" method="get">
                    <input type="hidden" name="dept_no" value="<?= $current_dept['dept_no'] ?>">
                    <button type="submit" class="btn btn-secondary">Retour à la liste des employés</button>
                </form>
                <?php endif; ?>
                <form action="../index.php" method="get">
                    <button type="submit" class="btn btn-dark">Retour à la liste des départements</button>
                </form>
            </div>
        </footer>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>