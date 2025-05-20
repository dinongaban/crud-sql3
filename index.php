<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'konek.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (isset($_POST['tambah'])) {
    $id = (int) $_POST['id'];
    $porsi = (int) $_POST['porsi'];

    if ($id > 0 && $porsi > 0) {
        $query = $db->query("SELECT * FROM bahan WHERE id = $id");
        $bahan = $query->fetchArray(SQLITE3_ASSOC);

        if ($bahan) {
            if (isset($_SESSION['keranjang'][$id])) {
                $_SESSION['keranjang'][$id]['porsi'] += $porsi;
            } else {
                $_SESSION['keranjang'][$id] = [
                    'nama' => $bahan['nama'],
                    'harga' => $bahan['harga'],
                    'porsi' => $porsi
                ];
            }
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
}

$total = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['harga'] * $item['porsi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Jamuku - UTS</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h2>Pilih Bahan Jamu</h2>
    <form method="POST" action="">
        <label for="id">Bahan:</label>
        <select name="id" id="id" required>
            <?php
            $result = $db->query("SELECT * FROM bahan");
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['nama']} ({$row['jenis']}) - Rp{$row['harga']}</option>";
            }
            ?>
        </select>
        <br><br>
        <label for="porsi">Porsi:</label>
        <input type="number" name="porsi" id="porsi" min="1" value="1" required />
        <br><br>
        <button type="submit" name="tambah">Tambah ke Keranjang</button>
    </form>

    <h2>Keranjang Belanja</h2>
    <?php if (!empty($_SESSION['keranjang'])): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Bahan</th>
                    <th>Harga per Porsi</th>
                    <th>Porsi</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['keranjang'] as $id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama']) ?></td>
                        <td>Rp<?= number_format($item['harga']) ?></td>
                        <td><?= $item['porsi'] ?></td>
                        <td>Rp<?= number_format($item['harga'] * $item['porsi']) ?></td>
                        <td><a href="?hapus=<?= $id ?>" onclick="return confirm('Hapus bahan ini?')">Hapus</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total Bayar: Rp<?= number_format($total) ?></h3>
    <?php else: ?>
        <p>Keranjang kosong.</p>
    <?php endif; ?>
</body>
</html>
