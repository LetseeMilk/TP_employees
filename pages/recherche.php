<?php
include '../inc/connection.php';

$dept_no = $_GET['dept_no'] ?? '';
$nom_employe = $_GET['nom_employe'] ?? '';
$age_min = $_GET['age_min'] ?? '';
$age_max = $_GET['age_max'] ?? '';
$page = $_GET['page'] ?? 0;
$offset = $page * 20;

$query = "
    SELECT e.emp_no, e.first_name, e.last_name, e.birth_date, e.hire_date, d.dept_name
    FROM employees e
    LEFT JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date > NOW()
    LEFT JOIN departments d ON de.dept_no = d.dept_no
    WHERE 1=1
";

if (!empty($dept_no)) {
    $query .= " AND de.dept_no = '$dept_no'";
}

if (!empty($nom_employe)) {
    $query .= " AND (e.last_name LIKE '%$nom_employe%' OR e.first_name LIKE '%$nom_employe%')";
}

if (!empty($age_min)) {
    $query .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= $age_min";
}

if (!empty($age_max)) {
    $query .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= $age_max";
}

$query .= " ORDER BY e.last_name, e.first_name LIMIT $offset, 20";

$result = mysqli_query($dataBase, $query);

$count_query = preg_replace('/LIMIT \d+, \d+/', '', $query);
$count_query = "SELECT COUNT(*) as total FROM ($count_query) as subquery";
$count_result = mysqli_query($dataBase, $count_query);
$total_employees = mysqli_fetch_assoc($count_result)['total'];

$departments_query = "SELECT dept_no, dept_name FROM departments ORDER BY dept_name";
$departments_result = mysqli_query($dataBase, $departments_query);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'employés</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <header class="mb-4">
            <h1 class="text-center">Recherche d'employés</h1>
        </header>

        <main>
            <form class="row g-3 mb-4" method="get" action="recherche.php">
                <div class="col-md-4">
                    <label for="dept_no" class="form-label">Département</label>
                    <select class="form-select" name="dept_no" id="dept_no">
                        <option value="">Tous les départements</option>
                        <?php while ($dept = mysqli_fetch_assoc($departments_result)): ?>
                        <option value="<?= $dept['dept_no'] ?>" <?= $dept_no == $dept['dept_no'] ? 'selected' : '' ?>>
                            <?= $dept['dept_name'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="nom_employe" class="form-label">Nom ou prénom</label>
                    <input type="text" class="form-control" name="nom_employe" id="nom_employe" value="<?= $nom_employe ?>">
                </div>

                <div class="col-md-2">
                    <label for="age_min" class="form-label">Âge minimum</label>
                    <input type="number" class="form-control" name="age_min" id="age_min" value="<?= $age_min ?>">
                </div>

                <div class="col-md-2">
                    <label for="age_max" class="form-label">Âge maximum</label>
                    <input type="number" class="form-control" name="age_max" id="age_max" value="<?= $age_max ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Numéro</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date de naissance</th>
                            <th>Âge</th>
                            <th>Département</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($employee = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $employee['emp_no'] ?></td>
                            <td><?= $employee['last_name'] ?></td>
                            <td><?= $employee['first_name'] ?></td>
                            <td><?= $employee['birth_date'] ?></td>
                            <td><?= date_diff(date_create($employee['birth_date']), date_create('today'))->y ?></td>
                            <td><?= $employee['dept_name'] ?? 'Non affecté' ?></td>
                            <td>
                                <form action="fiche_employe.php" method="get" class="d-inline">
                                    <input type="hidden" name="emp_no" value="<?= $employee['emp_no'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Voir fiche</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?php if ($page > 0): ?>
                <form action="recherche.php" method="get" class="d-inline">
                    <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                    <input type="hidden" name="nom_employe" value="<?= $nom_employe ?>">
                    <input type="hidden" name="age_min" value="<?= $age_min ?>">
                    <input type="hidden" name="age_max" value="<?= $age_max ?>">
                    <input type="hidden" name="page" value="<?= $page - 1 ?>">
                    <button type="submit" class="btn btn-secondary">Précédent</button>
                </form>
                <?php else: ?>
                <span></span>
                <?php endif; ?>

                <?php if (($offset + 20) < $total_employees): ?>
                <form action="recherche.php" method="get" class="d-inline">
                    <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                    <input type="hidden" name="nom_employe" value="<?= $nom_employe ?>">
                    <input type="hidden" name="age_min" value="<?= $age_min ?>">
                    <input type="hidden" name="age_max" value="<?= $age_max ?>">
                    <input type="hidden" name="page" value="<?= $page + 1 ?>">
                    <button type="submit" class="btn btn-secondary">Suivant</button>
                </form>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mt-4">Aucun employé trouvé avec ces critères de recherche.</div>
            <?php endif; ?>
        </main>

        <footer class="mt-5 text-center">
            <form action="../index.php" method="get">
                <button class="btn btn-outline-dark" type="submit">Retour à la liste des départements</button>
            </form>
        </footer>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>