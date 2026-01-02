<?php 
include 'baglan.php'; 
// Giriş kontrolü
if(!isset($_SESSION['kullanici'])) header("Location: index.php"); 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Kulüp Duyuruları</title>
</head>
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
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>📢 Güncel Duyurular</h2>
        [cite_start]<?php if($_SESSION['kullanici']['rol_id'] == 2): // Kulüp Başkanı ise duyuru ekleme butonu göster[cite: 38]?>
            <a href="duyuru_ekle.php" class="btn">Yeni Duyuru Yayınla</a>
        <?php endif; ?>
    </div>

    <?php
    // SQL5.sql Sorgu 4 mantığı ile duyuruları ve kulüp bilgilerini çekiyoruz
    $sorgu = "SELECT d.baslik, d.icerik, d.yayin_tarihi, k.kulup_adi, u.ad_soyad AS baskan_adi 
              FROM Duyurular d
              JOIN Kulupler k ON d.kulup_id = k.kulup_id
              JOIN Kullanicilar u ON k.baskan_id = u.kullanici_id
              ORDER BY d.yayin_tarihi DESC";
    
    $sonuc = $baglan->query($sorgu);

    if ($sonuc->num_rows > 0) {
        while($duyuru = $sonuc->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<h3>" . htmlspecialchars($duyuru['baslik']) . "</h3>";
            echo "<p style='color: #666; font-size: 0.9em;'><strong>Yayınlayan:</strong> " . htmlspecialchars($duyuru['kulup_adi']) . " (" . htmlspecialchars($duyuru['baskan_adi']) . ")</p>";
            echo "<hr style='border: 0; border-top: 1px solid #eee; margin: 10px 0;'>";
            echo "<p>" . nl2br(htmlspecialchars($duyuru['icerik'])) . "</p>";
            echo "<p style='text-align: right; font-size: 0.8em; color: #999;'>📅 Tarih: " . $duyuru['yayin_tarihi'] . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='card'><p>Henüz yayınlanmış bir duyuru bulunmuyor.</p></div>";
    }
    ?>
</div>

</body>
</html>