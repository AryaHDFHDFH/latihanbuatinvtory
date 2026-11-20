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

$barangObj = new Barang($db);
$id = (int) ($_GET["id"] ?? 0);
$item = $barangObj->find($id);

if (!$item) {
    $_SESSION["flash"] = ["ok" => false, "message" => "Data barang tidak ditemukan."];
    header("Location: dashboard.php");
    exit;
}

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item["kode_barang"] = $_POST["kode_barang"] ?? "";
    $item["nama_barang"] = $_POST["nama_barang"] ?? "";
    $item["kategori"] = $_POST["kategori"] ?? "";
    $item["stok"] = (int) ($_POST["stok"] ?? 0);
    $item["harga"] = (int) ($_POST["harga"] ?? 0);

    $res = $barangObj->update(
        $id,
        $item["kode_barang"],
        $item["nama_barang"],
        $item["kategori"],
        $item["stok"],
        $item["harga"]
    );

    if ($res["ok"]) {
        $_SESSION["flash"] = $res;
        header("Location: dashboard.php");
        exit;
    }

    $error = $res["message"];
}

require_once __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Edit Barang</h5>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control"
                               value="<?= e($item["kode_barang"]) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                               value="<?= e($item["nama_barang"]) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control"
                               value="<?= e($item["kategori"]) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" min="0"
                                   value="<?= (int) $item["stok"] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" min="0"
                                   value="<?= (int) $item["harga"] ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>