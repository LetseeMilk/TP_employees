<?php
require 'connection.php';
function afficherDepartements(mysqli $db): array {
    $query = "SELECT * FROM v_departements_complets";
    $result = mysqli_query($db, $query);

    $departements = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $departements[] = $row;
    }
    return $departements;
}

function getEmployesParDepartement(mysqli $db, string $dept_no, int $page = 0): array {
    $limit = 20;
    $offset = $page * $limit;

   
    $dept_result = mysqli_query($db, "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'");
    $dept_info = mysqli_fetch_assoc($dept_result);
    if (isset($dept_info['dept_name'])) {
    $dept_name = $dept_info['dept_name'];
    } else {
        $dept_name = 'Inconnu';
    }

    $query = "
        SELECT emp_no, first_name, last_name, hire_date
        FROM v_employes_par_dept
        WHERE dept_no = '$dept_no'
        ORDER BY last_name, first_name
        LIMIT $offset, $limit
    ";
    $result = mysqli_query($db, $query);

    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }

    $count_result = mysqli_query($db, "
        SELECT COUNT(*) as total
        FROM v_employes_par_dept
        WHERE dept_no = '$dept_no'
    ");
    $total = mysqli_fetch_assoc($count_result)['total'];

    return [
        'dept_name' => $dept_name,
        'employees' => $employees,
        'total' => $total
    ];
}

function getFicheEmploye(mysqli $db, int $emp_no): array {

    $employee_result = mysqli_query($db, "SELECT * FROM v_employes_fiche WHERE emp_no = $emp_no");
    $employee = mysqli_fetch_assoc($employee_result);


    $salaries = [];
    $salary_result = mysqli_query($db, "
        SELECT * FROM v_salaries_all 
        WHERE emp_no = $emp_no
        ORDER BY from_date DESC
    ");
    while ($row = mysqli_fetch_assoc($salary_result)) {
        $salaries[] = $row;
    }


    $titles = [];
    $title_result = mysqli_query($db, "
        SELECT * FROM v_titles_all 
        WHERE emp_no = $emp_no
        ORDER BY from_date DESC
    ");
    while ($row = mysqli_fetch_assoc($title_result)) {
        $titles[] = $row;
    }


    $longest_title_result = mysqli_query($db, "
        SELECT title, duree_jours
        FROM v_titles_duree
        WHERE emp_no = $emp_no
        ORDER BY duree_jours DESC
        LIMIT 1
    ");
    $longest_title = mysqli_fetch_assoc($longest_title_result);

    $dept_no = null;
    $dept_name = null;

    if (isset($employee['dept_no'])) {
        $dept_no = $employee['dept_no'];
    } else {
        $dept_no = null;
    }

    if (isset($employee['dept_name'])) {
        $dept_name = $employee['dept_name'];
    } else {
        $dept_name = null;
    }

    return [
        'employee' => $employee,
        'salaries' => $salaries,
        'titles' => $titles,
        'current_dept' => [
            'dept_no' => $dept_no,
            'dept_name' => $dept_name,
        ],
        'longest_title' => $longest_title,
    ];

}

function rechercherEmployes(mysqli $db, array $criteres, int $offset = 0): array {
    $limit = 20;
    $conditions = "1=1";

    if (!empty($criteres['dept_no'])) {
        $conditions .= " AND dept_no = '" . $criteres['dept_no'] . "'";
    }

    if (!empty($criteres['nom_employe'])) {
        $nom = $criteres['nom_employe'];
        $conditions .= " AND (last_name LIKE '%$nom%' OR first_name LIKE '%$nom%')";
    }

    if (!empty($criteres['age_min'])) {
        $age_min = (int) $criteres['age_min'];
        $conditions .= " AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= $age_min";
    }

    if (!empty($criteres['age_max'])) {
        $age_max = (int) $criteres['age_max'];
        $conditions .= " AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= $age_max";
    }

    $query = "
        SELECT * FROM v_employes_departements
        WHERE $conditions
        ORDER BY last_name, first_name
        LIMIT $offset, $limit
    ";
    $result = mysqli_query($db, $query);
    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }

    $count_query = "
        SELECT COUNT(*) as total FROM v_employes_departements WHERE $conditions
    ";
    $count_result = mysqli_query($db, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    return ['employees' => $employees, 'total' => $total];
}

function getAllDepartments(mysqli $db): array {
    $result = mysqli_query($db, "SELECT dept_no, dept_name FROM departments ORDER BY dept_name");
    $departments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    return $departments;
}

function getStatistiquesParEmploi(mysqli $db): array {
    $result = mysqli_query($db, "SELECT * FROM v_statistiques_par_emploi");
    $stats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[] = $row;
    }
    return $stats;
}


?>
