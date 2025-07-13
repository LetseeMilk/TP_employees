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
    $current_dept_date = null;
    if (isset($employee['dept_no'])) {
        $date_result = mysqli_query($db, "
            SELECT from_date 
            FROM dept_emp 
            WHERE emp_no = $emp_no 
            AND dept_no = '{$employee['dept_no']}' 
            AND to_date > NOW()
            LIMIT 1
        ");
        if ($date_result && $date_row = mysqli_fetch_assoc($date_result)) {
            $current_dept_date = $date_row['from_date'];
        }
    }

    return [
        'employee' => $employee,
        'salaries' => $salaries,
        'titles' => $titles,
        'current_dept' => [
            'dept_no' => $dept_no,
            'dept_name' => $dept_name,
            'from_date' => $current_dept_date,
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

function changerDepartementEmploye(mysqli $db, array $postData): array {

    if (isset($postData['emp_no'])) {
        $emp_no = $postData['emp_no'];
    } else {
        $emp_no = null;
    }

    if (isset($postData['new_dept_no'])) {
        $new_dept_no = $postData['new_dept_no'];
    } else {
        $new_dept_no = null;
    }

    if (isset($postData['from_date'])) {
        $from_date = $postData['from_date'];
    } else {
        $from_date = null;
    }

    if (isset($postData['current_dept_no'])) {
        $current_dept_no = $postData['current_dept_no'];
    } else {
        $current_dept_no = null;
    }

    if (isset($postData['current_from_date'])) {
        $current_from_date = $postData['current_from_date'];
    } else {
        $current_from_date = null;
    }


    if (!$emp_no || !$new_dept_no || !$from_date) {
        $errors[] = 'Tous les champs sont obligatoires';
    }

    if ($current_from_date && strtotime($from_date) < strtotime($current_from_date)) {
        $errors[] = 'La date de début ne peut pas être antérieure à la date actuelle (' . $current_from_date . ')';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }


    if ($current_dept_no) {
        $update_current = mysqli_query($db, "
            UPDATE dept_emp 
            SET to_date = DATE_SUB('$from_date', INTERVAL 1 DAY)
            WHERE emp_no = $emp_no 
            AND dept_no = '$current_dept_no'
            AND to_date > NOW()
        ");
        
        if (!$update_current) {
            $errors[] = 'Erreur lors de la mise à jour du département actuel';
            return ['success' => false, 'errors' => $errors];
        }
    }

    $insert_new = mysqli_query($db, "
        INSERT INTO dept_emp (emp_no, dept_no, from_date, to_date)
        VALUES ($emp_no, '$new_dept_no', '$from_date', '9999-01-01')
    ");

    if ($insert_new) {
        return ['success' => true, 'message' => 'Département changé avec succès'];
    } else {
        $errors[] = 'Erreur lors du changement de département';
        return ['success' => false, 'errors' => $errors];
    }
}

function getCurrentManager(mysqli $db, string $dept_no): ?array {
    $query = "SELECT e.first_name, e.last_name, dm.from_date 
              FROM dept_manager dm
              JOIN employees e ON dm.emp_no = e.emp_no
              WHERE dm.dept_no = '$dept_no' 
              AND dm.to_date > NOW()";
    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result) ?: null;
}

function devenirManager(mysqli $db, array $data): array {
    // Validation
    $errors = [];
    if (empty($data['emp_no']) || empty($data['dept_no']) || empty($data['from_date'])) {
        $errors[] = "Tous les champs sont obligatoires";
        return ['success' => false, 'errors' => $errors];
    }

    $current_manager = getCurrentManager($db, $data['dept_no']);
  
    if ($current_manager && strtotime($data['from_date']) <= strtotime($current_manager['from_date'])) {
        $errors[] = "La date doit être postérieure au ".$current_manager['from_date'];
        return ['success' => false, 'errors' => $errors];
    }

    if ($current_manager) {
        mysqli_query($db, "UPDATE dept_manager 
                         SET to_date = DATE_SUB('{$data['from_date']}', INTERVAL 1 DAY)
                         WHERE dept_no = '{$data['dept_no']}'
                         AND to_date > NOW()");
    }

    $insert = mysqli_query($db, "INSERT INTO dept_manager 
                               (emp_no, dept_no, from_date, to_date)
                               VALUES ({$data['emp_no']}, '{$data['dept_no']}', 
                               '{$data['from_date']}', '9999-01-01')");

    if ($insert) {
        return ['success' => true, 'message' => "Vous êtes maintenant manager de ce département"];
    } else {
        $errors[] = "Erreur lors de la mise à jour";
        return ['success' => false, 'errors' => $errors];
    }
}

?>
