<?php
include '../inc/function.php';

$dept_no = $_GET['dept_no'] ?? '';
$page = $_GET['page'] ?? 0;

$data = getEmployesParDepartement($dataBase, $dept_no, $page);
$employees = $data['employees'];
$total = $data['total'];
$dept_name = $data['dept_name'];
$offset = $page * 20;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Employés du département</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <header class="text-center mb-4">
            <h1 class="fw-bold">Employés du département : <?= $dept_name ?></h1>
        </header>

        <main>
            <?php if (count($employees) > 0): ?>
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
                        <?php foreach ($employees as $e): ?>
                        <tr>
                            <td><?= $e['emp_no'] ?></td>
                            <td><?= $e['last_name'] ?></td>
                            <td><?= $e['first_name'] ?></td>
                            <td><?= $e['hire_date'] ?></td>
                            <td>
                                <form action="fiche_employe.php" method="get" class="d-inline">
                                    <input type="hidden" name="emp_no" value="<?= $e['emp_no'] ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Voir fiche</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <?php if ($page > 0): ?>
                <form method="get">
                    <input type="hidden" name="dept_no" value="<?= $dept_no ?>">
                    <input type="hidden" name="page" value="<?= $page - 1 ?>">
                    <button type="submit" class="btn btn-secondary">Précédent</button>
                </form>
                <?php else: ?><span></span><?php endif; ?>

                <?php if (($offset + 20) < $total): ?>
                <form method="get">
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
