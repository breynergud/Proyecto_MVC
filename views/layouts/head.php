<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Estricta separación de roles: módulos permitidos por cada rol
$rolePermissions = [
    'centro' => ['dashboard', 'sede', 'ambiente', 'programa', 'instructor', 'competencia', 'coordinacion', 'centro_formacion', 'titulo_programa'],
    'coordinador' => ['dashboard', 'programa', 'ficha', 'instructor', 'asignacion'],
    'instructor' => ['dashboard', 'asignacion']
];

$userRole = $_SESSION['user_role'] ?? '';
$currentPath = $_SERVER['PHP_SELF'];
$pathParts = explode('/', $currentPath);
// Detectar el módulo actual basado en la estructura de carpetas de /views/
$viewIndex = array_search('views', $pathParts);
$currentModule = ($viewIndex !== false && isset($pathParts[$viewIndex + 1])) ? $pathParts[$viewIndex + 1] : '';

// Validar permisos de acceso al módulo (exceptuando layouts y auth)
if ($currentModule && !in_array($currentModule, ['auth', 'layouts'])) {
    if (isset($rolePermissions[$userRole]) && !in_array($currentModule, $rolePermissions[$userRole])) {
        header('Location: ../dashboard/index.php?error=unauthorized');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'SENA Académico'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="../../assets/js/sede/tailwind-config.js"></script>
    <link href="../../assets/css/styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../assets/css/sidebar-green.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../../assets/css/dashboard.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">