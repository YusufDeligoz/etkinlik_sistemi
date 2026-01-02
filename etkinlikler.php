<?php include 'baglan.php'; ?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Etkinlikler</title></head>
<body>
<nav>
    <a href="anasayfa.php" style="color: #a33333;">Anasayfa</a> 
    <a href="etkinlikler.php">Etkinlikler</a> 
    <a href="duyurular.php">Duyurular</a> 
    
    <?php if($rol_id <= 2): // Admin(1) veya Başkan(2) ise ?>
        <a href="etkinlik_ekle.php" style="color:#f1c40f;">➕ Etkinlik Ekle</a>
        <a href="duyuru_ekle.php" style="color:#f1c40f;">➕ Duyuru Ekle</a>
    <?php endif; ?>

    <a href="profil.php">Profilim</a> 
    <a href="cikis.php" style="color:#ff4444;">Çıkış</a>
</nav>

<div class="container">
    <h2>Tüm Etkinlikler</h2>
    <table>
        <tr><th>Etkinlik</th><th>Kulüp</th><th>Kategori</th><th>Katılım</th><th>İşlem</th></tr>
        <?php
        $sorgu = "SELECT e.*, k.kulup_adi, cat.kategori_adi FROM Etkinlikler e 
                  JOIN Kulupler k ON e.kulup_id = k.kulup_id 
                  JOIN Kategoriler cat ON e.kategori_id = cat.kategori_id";
        $sonuc = $baglan->query($sorgu);
        while($satir = $sonuc->fetch_assoc()){
            echo "<tr>
                <td>{$satir['baslik']}</td>
                <td>{$satir['kulup_adi']}</td>
                <td>{$satir['kategori_adi']}</td>
                <td>{$satir['mevcut_katilim']} / {$satir['katilim_limiti']}</td>
                <td><a href='detay.php?id={$satir['etkinlik_id']}' class='btn'>Detay</a></td>
            </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>