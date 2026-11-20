<?php
require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/app/Auth.php";
require_once __DIR__ . "/app/Barang.php";

$db = (new Database())->connect();
$auth = new Auth($db);
$auth->checkLogin();

$barangObj = new Barang($db);
$isAdmin = $auth->isAdmin();
$summary = $barangObj->summary();
$items = $barangObj->all();

$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

require_once __DIR__ . "/includes/header.php";
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-0">Dashboard & Laporan Inventaris</h4>
        <small class="text-muted">
            Mode akses: <strong><?= $isAdmin ? "Administrator (Full CRUD)" : "User (Lihat & Laporan)" ?></strong>
        </small>
    </div>
    <div class="d-flex gap-2 no-print">
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            Cetak Laporan
        </button>
        <?php if ($isAdmin): ?>
            <a href="barang_tambah.php" class="btn btn-primary btn-sm">+ Tambah Barang</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash["ok"] ? "success" : "danger" ?> no-print">
        <?= e($flash["message"]) ?>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Jenis Barang</div>
                <div class="fs-4 fw-bold"><?= (int) $summary["total_item"] ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Unit Stok</div>
                <div class="fs-4 fw-bold"><?= (int) $summary["total_stok"] ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Stok Menipis (≤ 5)</div>
                <div class="fs-4 fw-bold text-danger"><?= (int) $summary["stok_menipis"] ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Nilai Aset</div>
                <div class="fs-5 fw-bold">Rp <?= number_format((int) $summary["total_nilai_aset"], 0, ",", ".") ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="card-title mb-3">Daftar Stok Barang</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <?php if ($isAdmin): ?>
                            <th class="no-print">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? 8 : 7 ?>" class="text-center text-muted py-4">
                                Belum ada data barang.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $i => $row): ?>
                            <?php $subtotal = (int) $row["stok"] * (int) $row["harga"]; ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><code><?= e($row["kode_barang"]) ?></code></td>
                                <td><?= e($row["nama_barang"]) ?></td>
                                <td><?= e($row["kategori"]) ?></td>
                                <td>
                                    <?= (int) $row["stok"] ?>
                                    <?php if ((int) $row["stok"] <= 5): ?>
                                        <span class="badge bg-danger ms-1">Menipis</span>
                                    <?php endif; ?>
                                </td>
                                <td>Rp <?= number_format((int) $row["harga"], 0, ",", ".") ?></td>
                                <td>Rp <?= number_format($subtotal, 0, ",", ".") ?></td>
                                <?php if ($isAdmin): ?>
                                    <td class="no-print">
                                        <a href="barang_edit.php?id=<?= (int) $row["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="barang_hapus.php?id=<?= (int) $row["id"] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>