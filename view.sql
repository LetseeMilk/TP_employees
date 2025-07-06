CREATE OR REPLACE VIEW v_departements_managers_actuels AS
SELECT 
    d.dept_no,
    d.dept_name,
    e.first_name,
    e.last_name
FROM 
    departments d
    JOIN dept_manager dm ON d.dept_no = dm.dept_no 
    JOIN employees e ON dm.emp_no = e.emp_no
WHERE 
    dm.to_date > NOW()
ORDER BY d.dept_name;


CREATE OR REPLACE VIEW v_employes_par_dept AS
SELECT 
    d.dept_no,
    d.dept_name,
    e.emp_no,
    e.first_name,
    e.last_name,
    e.hire_date
FROM 
    employees e
    JOIN dept_emp de ON e.emp_no = de.emp_no
    JOIN departments d ON de.dept_no = d.dept_no
WHERE 
    de.to_date > NOW();

CREATE OR REPLACE VIEW v_employes_fiche AS
SELECT 
    e.*,
    d.dept_no,
    d.dept_name
FROM 
    employees e
     JOIN current_dept_emp cde ON e.emp_no = cde.emp_no
     JOIN departments d ON cde.dept_no = d.dept_no;

CREATE OR REPLACE VIEW v_salaries_all AS
SELECT * FROM salaries;

CREATE OR REPLACE VIEW v_titles_all AS
SELECT * FROM titles;


CREATE OR REPLACE VIEW v_employes_departements AS
SELECT 
    e.emp_no,
    e.first_name,
    e.last_name,
    e.birth_date,
    e.hire_date,
    d.dept_no,
    d.dept_name
FROM 
    employees e
    JOIN dept_emp de ON e.emp_no = de.emp_no AND de.to_date > NOW()
    JOIN departments d ON de.dept_no = d.dept_no;


CREATE OR REPLACE VIEW v_departements_avec_compte AS
SELECT 
    d.dept_no,
    d.dept_name,
    COUNT(DISTINCT de.emp_no) AS nb_employes
FROM departments d
JOIN dept_emp de ON d.dept_no = de.dept_no AND de.to_date > NOW()
GROUP BY d.dept_no, d.dept_name
ORDER BY d.dept_name;

CREATE OR REPLACE VIEW v_statistiques_par_emploi AS
SELECT 
    t.title,
    COUNT(DISTINCT e.emp_no) AS total_employes,
    SUM(CASE WHEN e.gender = 'M' THEN 1 ELSE 0 END) AS hommes,
    SUM(CASE WHEN e.gender = 'F' THEN 1 ELSE 0 END) AS femmes,
    ROUND(AVG(s.salary), 2) AS salaire_moyen
FROM titles t
JOIN employees e ON t.emp_no = e.emp_no
JOIN salaries s ON s.emp_no = e.emp_no
    AND s.from_date <= t.to_date 
    AND (s.to_date >= t.from_date OR s.to_date IS NULL)
WHERE t.to_date = (
    SELECT MAX(t2.to_date)
    FROM titles t2
    WHERE t2.emp_no = t.emp_no
)
GROUP BY t.title
ORDER BY t.title;


CREATE OR REPLACE VIEW v_titles_duree AS
SELECT 
    emp_no,
    title,
    from_date,
    to_date,
    DATEDIFF(
        CASE 
            WHEN to_date IS NULL THEN CURDATE()
            ELSE to_date
        END,
        from_date
    ) AS duree_jours
FROM titles;

CREATE OR REPLACE VIEW v_departements_complets AS
SELECT 
    d.dept_no,
    d.dept_name,
    e.first_name,
    e.last_name,
    COUNT(DISTINCT de.emp_no) AS nb_employes
FROM    
    departments d
    JOIN dept_manager dm ON d.dept_no = dm.dept_no 
    JOIN employees e ON dm.emp_no = e.emp_no
    LEFT JOIN dept_emp de ON d.dept_no = de.dept_no AND de.to_date > NOW()
WHERE 
    dm.to_date > NOW() OR dm.to_date IS NULL
GROUP BY 
    d.dept_no, d.dept_name, e.first_name, e.last_name
ORDER BY 
    d.dept_name;