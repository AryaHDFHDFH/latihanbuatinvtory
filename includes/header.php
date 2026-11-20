<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

$currentUser = $_SESSION["user"] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark no-print mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="dashboard.php">Inventaris App</a>
        <?php if ($currentUser): ?>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small">
                    <?= e($currentUser["nama_lengkap"]) ?>
                    <span class="badge bg-secondary text-uppercase"><?= e($currentUser["role"]) ?></span>
                </span>
                <a href="index.php?action=logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container pb-5">