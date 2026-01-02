<?php include 'baglan.php'; 
$k_id = $_SESSION['kullanici']['kullanici_id'];
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Profilim</title></head>
<body>

<nav>
    <a href="anasayfa.php">🏠 Anasayfa</a> 
    <a href="etkinlikler.php">📅 Etkinlikler</a> 
    <a href="duyurular.php">📢 Duyurular</a> 
    
    <?php if($_SESSION['kullanici']['rol_id'] <= 2): // Admin(1) veya Başkan(2) ise ?>
        <a href="etkinlik_ekle.php" style="color:#f1c40f;">➕ Etkinlik Ekle</a>
        <a href="duyuru_ekle.php" style="color:#f1c40f;">➕ Duyuru Ekle</a>
    <?php endif; ?>

    <a href="profil.php">👤 Profilim</a> 
    <a href="cikis.php" style="color:#e74c3c;">🚪 Çıkış</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Profil Bilgileri</h2>
        <p>Ad Soyad: <?php echo $_SESSION['kullanici']['ad_soyad']; ?></p>
        <p>E-posta: <?php echo $_SESSION['kullanici']['email']; ?></p>
    </div>
    <h3>Katıldığım Etkinlikler</h3>
    <table>
        <tr><th>Etkinlik</th><th>Düzenleyen Kulüp</th><th>Tarih</th></tr>
        <?php
        $sonuc = $baglan->query("CALL sp_OgrenciEtkinlikleri($k_id)");
        while($satir = $sonuc->fetch_assoc()){
            echo "<tr><td>{$satir['Etkinlik']}</td><td>{$satir['kulup_adi']}</td><td>{$satir['kayit_tarihi']}</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>