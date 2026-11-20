<?php
$namaBarang = "Keyboard Mechanical";
$stok_barang = 10;
$hargaSatuan = 450000;
$isReady = true;

echo "NAMA BARANG : " . $namaBarang . "<br>";
echo "Harga : Rp. " . number_format($hargaSatuan, 0, ',', '.') . "<br>";

if ($stok_barang <= 5) {
    echo "Statuts : Stok Menipis (" . $stok_barang . " barang tersisa)<br>";
} else {
    echo "Statuts : Stok Aman (" . $stok_barang . " barang tersisa)<br>";
}

