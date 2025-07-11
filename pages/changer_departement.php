<?php
session_start();
include '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fiche_employe.php?emp_no=' . ($_POST['emp_no'] ?? ''));
    exit;
}

$result = changerDepartementEmploye($dataBase, $_POST);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = implode('<br>', $result['errors']);
}

header('Location: fiche_employe.php?emp_no=' . ($_POST['emp_no'] ?? ''));
exit;