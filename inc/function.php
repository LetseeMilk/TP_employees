<?php
require 'connection.php';

function afficherDepartements(mysqli $db) {
    $query = "
        SELECT d.dept_no, d.dept_name, e.first_name, e.last_name
        FROM departments d
        JOIN dept_manager dm ON d.dept_no = dm.dept_no 
        JOIN employees e ON dm.emp_no = e.emp_no
        WHERE dm.to_date > NOW()
        ORDER BY d.dept_name
    ";
    
    $result = mysqli_query($db, $query);

    if (!$result) {
        echo "<p class='text-danger'>Erreur de chargement des départements.</p>";
        return;
    }

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped align-middle">';
    echo '<thead class="table-light">
            <tr>
                <th>Numéro</th>
                <th>Nom du département</th>
                <th>Manager actuel</th>
                <th>Actions</th>
            </tr>
          </thead>';
    echo '<tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        $manager = trim($row['first_name'] . ' ' . $row['last_name']);
        if ($manager === '') $manager = 'Non défini';

        echo '<tr>';
        echo '<td>' . $row['dept_no'] . '</td>';
        echo '<td>' . $row['dept_name'] . '</td>';
        echo '<td>' . $manager . '</td>';
        echo '<td>
                <form action="pages/employes_departement.php" method="get" class="d-inline">
                    <input type="hidden" name="dept_no" value="' . $row['dept_no'] . '">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Voir les employés</button>
                </form>
              </td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}
function getEmployesParDepartement(mysqli $db, string $dept_no, int $page = 0): array {
    $offset = $page * 20;

    $dept_query = "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'";
    $dept_result = mysqli_query($db, $dept_query);
    $dept_info = mysqli_fetch_assoc($dept_result);

    $employees_query = "
        SELECT e.emp_no, e.first_name, e.last_name, e.hire_date
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        WHERE de.dept_no = '$dept_no' AND de.to_date > NOW()
        ORDER BY e.last_name, e.first_name
        LIMIT $offset, 20
    ";
    $employees_result = mysqli_query($db, $employees_query);
    
    $employees = [];
    while ($row = mysqli_fetch_assoc($employees_result)) {
        $employees[] = $row;
    }


    $count_query = "
        SELECT COUNT(*) as total 
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        WHERE de.dept_no = '$dept_no' AND de.to_date > NOW()
    ";
    $count_result = mysqli_query($db, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    return [
        'dept_name' => $dept_info['dept_name'] ?? 'Inconnu',
        'employees' => $employees,
        'total' => $total
    ];
}
function getFicheEmploye(mysqli $db, int $emp_no): array {

    $employee_query = "SELECT * FROM employees WHERE emp_no = $emp_no";
    $employee_result = mysqli_query($db, $employee_query);
    $employee = mysqli_fetch_assoc($employee_result);

    $salaries_query = "SELECT * FROM salaries WHERE emp_no = $emp_no ORDER BY from_date DESC";
    $salaries_result = mysqli_query($db, $salaries_query);
    $salaries = [];
    while ($row = mysqli_fetch_assoc($salaries_result)) {
        $salaries[] = $row;
    }

    $titles_query = "SELECT * FROM titles WHERE emp_no = $emp_no ORDER BY from_date DESC";
    $titles_result = mysqli_query($db, $titles_query);
    $titles = [];
    while ($row = mysqli_fetch_assoc($titles_result)) {
        $titles[] = $row;
    }


    $current_dept_query = "
        SELECT d.dept_no, d.dept_name 
        FROM departments d
        JOIN dept_emp de ON d.dept_no = de.dept_no
        WHERE de.emp_no = $emp_no AND de.to_date > NOW()
    ";
    $current_dept_result = mysqli_query($db, $current_dept_query);
    $current_dept = mysqli_fetch_assoc($current_dept_result);

    return [
        'employee' => $employee,
        'salaries' => $salaries,
        'titles' => $titles,
        'current_dept' => $current_dept,
    ];
}

function rechercherEmployes(mysqli $db, array $criteres, int $offset = 0): array {
    $dept_no = $criteres['dept_no'] ?? '';
    $nom_employe = $criteres['nom_employe'] ?? '';
    $age_min = $criteres['age_min'] ?? '';
    $age_max = $criteres['age_max'] ?? '';

    $query = "
        SELECT e.emp_no, e.first_name, e.last_name, e.birth_date, e.hire_date, d.dept_name
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date > NOW()
        JOIN departments d ON de.dept_no = d.dept_no
        WHERE 1=1
    ";

    if (!empty($dept_no)) {
        $query .= " AND de.dept_no = '" . mysqli_real_escape_string($db, $dept_no) . "'";
    }

    if (!empty($nom_employe)) {
        $safe_nom = mysqli_real_escape_string($db, $nom_employe);
        $query .= " AND (e.last_name LIKE '%$safe_nom%' OR e.first_name LIKE '%$safe_nom%')";
    }

    if (!empty($age_min)) {
        $query .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= " . (int)$age_min;
    }

    if (!empty($age_max)) {
        $query .= " AND TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= " . (int)$age_max;
    }

    $query .= " ORDER BY e.last_name, e.first_name LIMIT $offset, 20";
    $result = mysqli_query($db, $query);

    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }

    
    $count_query = preg_replace('/LIMIT \d+, \d+/', '', $query);
    $count_query = "SELECT COUNT(*) as total FROM ($count_query) as subquery";
    $count_result = mysqli_query($db, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    return ['employees' => $employees, 'total' => $total];
}

function getAllDepartments(mysqli $db): array {
    $departments = [];
    $result = mysqli_query($db, "SELECT dept_no, dept_name FROM departments ORDER BY dept_name");
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    return $departments;
}


?>