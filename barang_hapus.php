<?php
require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/app/Auth.php";
require_once __DIR__ . "/app/Barang.php";

$db = (new Database())->connect();
$auth = new Auth($db);
$auth->checkLogin();

if (!$auth->isAdmin()) {
    $_SESSION["flash"] = ["ok" => false, "message" => "Akses ditolak. Fitur ini khusus Admin."];
    header("Location: dashboard.php");
    exit;
}

$id = (int) ($_GET["id"] ?? 0);
$barangObj = new Barang($db);
$_SESSION["flash"] = $barangObj->delete($id);

header("Location: dashboard.php");
exit;