<?php
require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/app/Auth.php";

$db = (new Database())->connect();
$auth = new Auth($db);

if (isset($_GET["action"]) && $_GET["action"] === "logout") {
    $auth->logout();
}

if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit;
}

$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mode = $_POST["mode"] ?? "login";

    if ($mode === "register") {
        $res = $auth->register(
            $_POST["nama_lengkap"] ?? "",
            $_POST["username"] ?? "",
            $_POST["password"] ?? ""
        );
    } else {
        $res = $auth->login(
            $_POST["username"] ?? "",
            $_POST["password"] ?? ""
        );
    }

    if ($res["ok"] && $mode === "login") {
        header("Location: dashboard.php");
        exit;
    }

    $_SESSION["flash"] = $res;
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center mt-4">
    <div class="col-md-10 col-lg-8">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash["ok"] ? "success" : "danger" ?>">
                <?= e($flash["message"]) ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Login</h5>
                        <form method="POST">
                            <input type="hidden" name="mode" value="login">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Register Akun User</h5>
                        <form method="POST">
                            <input type="hidden" name="mode" value="register">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (min. 6 karakter)</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-outline-dark w-100">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>