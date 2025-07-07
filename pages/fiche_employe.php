<?php
include '../inc/function.php';

$emp_no = $_GET['emp_no'] ?? null;

if (!$emp_no || !is_numeric($emp_no)) {
    die('Numéro employé invalide.');
}

$data = getFicheEmploye($dataBase, (int)$emp_no);
$employee = $data['employee'];
$salaries = $data['salaries'];
$titles = $data['titles'];
$current_dept = $data['current_dept'];

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche employé <?= ($employee['first_name'] . ' ' . $employee['last_name']) ?></title>
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
        <?php if (!empty($data['longest_title'])): ?>
        <p><strong>Emploi le plus long :</strong> 
            <?= ($data['longest_title']['title']) ?> 
        </p>
        <?php endif; ?>
    </div>
</div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">Historique des salaires</div>
                <div class="card-body">
                    <?php if (count($salaries) > 0): ?>

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
                                <?php foreach ($salaries as $salary): ?>

                                <tr>
                                    <td><?= $salary['salary'] ?> $</td>
                                    <td><?= $salary['from_date'] ?></td>
                                    <td><?= $salary['to_date'] ?></td>
                                </tr>
                                <?php endforeach; ?>
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
                    <?php if (count($titles) > 0): ?>
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
                                <?php foreach ($titles as $title): ?>

                                <tr>
                                    <td><?= $title['title'] ?></td>
                                    <td><?= $title['from_date'] ?></td>
                                    <td><?= $title['to_date'] ?></td>
                                </tr>
                                <?php endforeach; ?>
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