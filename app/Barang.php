<?php

class Barang
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM barang ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id = :id LIMIT 1");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $kode, string $nama, string $kategori, int $stok, int $harga): array
    {
        $kode = strtoupper(trim($kode));
        $nama = trim($nama);
        $kategori = trim($kategori);

        if ($kode === "" || $nama === "" || $kategori === "" || $stok < 0 || $harga < 0) {
            return ["ok" => false, "message" => "Data tidak valid. Pastikan semua field terisi dan stok/harga tidak negatif."];
        }

        $cek = $this->db->prepare("SELECT id FROM barang WHERE kode_barang = :kode LIMIT 1");
        $cek->execute(["kode" => $kode]);
        if ($cek->fetch()) {
            return ["ok" => false, "message" => "Kode barang sudah digunakan."];
        }

        $stmt = $this->db->prepare(
            "INSERT INTO barang (kode_barang, nama_barang, kategori, stok, harga)
             VALUES (:kode, :nama, :kategori, :stok, :harga)"
        );
        $stmt->execute([
            "kode" => $kode,
            "nama" => $nama,
            "kategori" => $kategori,
            "stok" => $stok,
            "harga" => $harga,
        ]);

        return ["ok" => true, "message" => "Barang berhasil ditambahkan."];
    }

    public function update(int $id, string $kode, string $nama, string $kategori, int $stok, int $harga): array
    {
        $kode = strtoupper(trim($kode));
        $nama = trim($nama);
        $kategori = trim($kategori);

        if ($id <= 0 || $kode === "" || $nama === "" || $kategori === "" || $stok < 0 || $harga < 0) {
            return ["ok" => false, "message" => "Data edit tidak valid."];
        }

        $cek = $this->db->prepare("SELECT id FROM barang WHERE kode_barang = :kode AND id != :id LIMIT 1");
        $cek->execute(["kode" => $kode, "id" => $id]);
        if ($cek->fetch()) {
            return ["ok" => false, "message" => "Kode barang sudah dipakai barang lain."];
        }

        $stmt = $this->db->prepare(
            "UPDATE barang
             SET kode_barang = :kode, nama_barang = :nama, kategori = :kategori, stok = :stok, harga = :harga
             WHERE id = :id"
        );
        $stmt->execute([
            "id" => $id,
            "kode" => $kode,
            "nama" => $nama,
            "kategori" => $kategori,
            "stok" => $stok,
            "harga" => $harga,
        ]);

        return ["ok" => true, "message" => "Data barang berhasil diperbarui."];
    }

    public function delete(int $id): array
    {
        if ($id <= 0) {
            return ["ok" => false, "message" => "ID barang tidak valid."];
        }

        $stmt = $this->db->prepare("DELETE FROM barang WHERE id = :id");
        $stmt->execute(["id" => $id]);

        return ["ok" => true, "message" => "Barang berhasil dihapus."];
    }

    public function summary(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total_item,
                COALESCE(SUM(stok), 0) AS total_stok,
                COALESCE(SUM(CASE WHEN stok <= 5 THEN 1 ELSE 0 END), 0) AS stok_menipis,
                COALESCE(SUM(stok * harga), 0) AS total_nilai_aset
             FROM barang"
        );
        return $stmt->fetch() ?: [
            "total_item" => 0,
            "total_stok" => 0,
            "stok_menipis" => 0,
            "total_nilai_aset" => 0,
        ];
    }
}