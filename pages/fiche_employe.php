<?php
session_start();
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

        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); endif; ?>
            </div>
        </div>

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

        <!-- bouton pour changer de departement by Harena-->
        <?php if ($current_dept): ?>
        <p>
            <strong>Département actuel :</strong> 
            <?= $current_dept['dept_name'] ?> (depuis <?= $current_dept['from_date'] ?? 'date inconnue' ?>)
            <button class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#changeDeptModal">
                Changer de département
            </button>
            <!-- bouton pour devenir manager by Harena-->
            <button class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#becomeManagerModal">
            Devenir Manager
        </button>
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

    <!-- petit fenetre avec formulaire demander v4 pour changer de departement by Harena-->
<div class="modal fade" id="changeDeptModal" tabindex="-1" aria-labelledby="changeDeptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="changer_departement.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeDeptModalLabel">Changer de département</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="emp_no" value="<?= $emp_no ?>">
                    <?php if ($current_dept): ?>
                    <input type="hidden" name="current_dept_no" value="<?= $current_dept['dept_no'] ?>">
                    <input type="hidden" name="current_from_date" value="<?= $current_dept['from_date'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Département actuel</label>
                        <input type="text" class="form-control" value="<?= $current_dept['dept_name'] ?>" readonly>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="new_dept_no" class="form-label">Nouveau département</label>
                        <select class="form-select" name="new_dept_no" id="new_dept_no" required>
                            <option value="">Sélectionnez un département</option>
                            <?php 
                            $departments = getAllDepartments($dataBase);
                            foreach ($departments as $dept): 
                                if ($current_dept && $dept['dept_no'] === $current_dept['dept_no']) continue;
                            ?>
                            <option value="<?= $dept['dept_no'] ?>"><?= $dept['dept_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="from_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="becomeManagerModal" tabindex="-1" aria-labelledby="becomeManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="devenir_manager.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="becomeManagerModalLabel">Devenir Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="emp_no" value="<?= $emp_no ?>">
                    <input type="hidden" name="dept_no" value="<?= $current_dept['dept_no'] ?>">
                    
                    <?php 
                    // Récupérer le manager actuel
                    $current_manager = getCurrentManager($dataBase, $current_dept['dept_no']);
                    ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Manager actuel</label>
                        <input type="text" class="form-control" 
                               value="<?= $current_manager ? htmlspecialchars($current_manager['first_name'].' '.$current_manager['last_name']) : 'Aucun manager' ?>" 
                               readonly>
                        <?php if ($current_manager): ?>
                        <small class="text-muted">En poste depuis <?= $current_manager['from_date'] ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="manager_from_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" name="from_date" id="manager_from_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>