<?php 
ob_start();
session_start();
include 'baglan.php'; 

if(!isset($_SESSION['kullanici'])) {
    header("Location: index.php");
    exit();
}

$k_id = $_SESSION['kullanici']['kullanici_id'];
$rol_id = $_SESSION['kullanici']['rol_id'];

// Filtreleme Parametresi
$kulup_filtre = isset($_GET['kulup']) ? (int)$_GET['kulup'] : 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyurular - SocialUni</title>
    <style>
        /* İSTÜN STİLİ TEMELLERİ */
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        
        nav { 
            background-color: #a33333; padding: 0 5%; height: 60px;
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000;
        }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; transition: 0.3s; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }

        .container { padding: 40px 5%; max-width: 900px; margin: auto; }

        /* ÜST BAŞLIK */
        .page-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 25px; border-left: 5px solid #a33333; padding-left: 15px;
        }
        .page-header h2 { margin: 0; color: #fff; font-size: 24px; }
        
        .btn-yayinla {
            background: #a33333; color: white; text-decoration: none; padding: 10px 20px;
            border-radius: 6px; font-weight: bold; font-size: 13px; transition: 0.3s;
        }

        /* KULÜP FİLTRELEME PANELİ */
        .filter-section {
            background: #1e1e1e; padding: 20px; border-radius: 12px; border: 1px solid #333; margin-bottom: 30px;
        }
        .club-filter-form { display: flex; align-items: center; gap: 10px; }
        label { font-size: 14px; color: #888; white-space: nowrap; }
        select { 
            background: #252525; color: white; border: 1px solid #444; padding: 10px; 
            border-radius: 8px; font-size: 14px; outline: none; flex-grow: 1;
        }
        .btn-filter { background: #444; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-filter:hover { background: #a33333; }

        /* DUYURU KARTLARI */
        .duyuru-card {
            background: #1e1e1e; border-radius: 12px; border: 1px solid #333;
            padding: 25px; margin-bottom: 25px; border-left: 4px solid #a33333;
            transition: 0.3s;
        }
        .duyuru-card:hover { border-color: #a33333; transform: translateX(5px); }
        .duyuru-card h3 { margin: 0 0 10px 0; color: #ff4d4d; font-size: 20px; }
        .meta-info { font-size: 13px; color: #888; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #2a2a2a; }
        .duyuru-content { line-height: 1.6; color: #bbb; }
        .date-tag { text-align: right; margin-top: 15px; font-size: 12px; color: #666; font-style: italic; }

        .empty-state { text-align: center; padding: 50px; background: #1e1e1e; border-radius: 12px; color: #666; border: 1px dashed #444; }
    </style>
</head>
<body>

<nav>
    <div class="nav-links">
        <a href="anasayfa.php">Anasayfa</a> 
        <a href="etkinlikler.php">Etkinlikler</a> 
        <a href="duyurular.php">Duyurular</a> 
        <a href="profil.php">Profilim</a> 
        
        <?php if(isset($_SESSION['kullanici']) && $_SESSION['kullanici']['rol_id'] == 1): ?>
            <a href="istatistik.php" style="color:#f1c40f; font-weight:bold;">İstatistikler</a>
        <?php endif; ?>
        
        <a href="cikis.php" style="color:#ff6666;">Çıkış</a>
    </div>
    <div class="logo">SocialUni</div>
</nav>

<div class="container">
    <div class="page-header">
        <h2>📢 Duyuru Panosu</h2>
        <?php if((int)$rol_id <= 2): ?>
            <a href="duyuru_ekle.php" class="btn-yayinla">Yeni Duyuru Yayınla</a>
        <?php endif; ?>
    </div>

    <div class="filter-section">
        <form action="duyurular.php" method="GET" class="club-filter-form">
            <label>Kulüp Seçiniz:</label>
            <select name="kulup">
                <option value="0">Tüm Kulüpler</option>
                <?php
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($klp = $kulupler->fetch_assoc()){
                    $sel = ($kulup_filtre == $klp['kulup_id']) ? 'selected' : '';
                    echo "<option value='{$klp['kulup_id']}' {$sel}>" . htmlspecialchars($klp['kulup_adi']) . "</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-filter">Duyuruları Filtrele</button>
        </form>
    </div>

    <?php
    // Sadece Kulübe Göre Filtreli SQL Sorgusu
    $sorgu = "SELECT d.*, k.kulup_adi, u.ad_soyad AS baskan_adi 
              FROM Duyurular d
              JOIN Kulupler k ON d.kulup_id = k.kulup_id
              JOIN Kullanicilar u ON k.baskan_id = u.kullanici_id";
    
    if($kulup_filtre > 0) {
        $sorgu .= " WHERE d.kulup_id = $kulup_filtre";
    }
    
    $sorgu .= " ORDER BY d.yayin_tarihi DESC";
    $sonuc = $baglan->query($sorgu);

    if ($sonuc && $sonuc->num_rows > 0) {
        while($duyuru = $sonuc->fetch_assoc()) {
            echo "<div class='duyuru-card'>";
            echo "<h3>" . htmlspecialchars($duyuru['baslik']) . "</h3>";
            echo "<div class='meta-info'>🏛️ <b>" . htmlspecialchars($duyuru['kulup_adi']) . "</b> | 👤 " . htmlspecialchars($duyuru['baskan_adi']) . "</div>";
            echo "<div class='duyuru-content'>" . nl2br(htmlspecialchars($duyuru['icerik'])) . "</div>";
            echo "<div class='date-tag'>🕒 " . $duyuru['yayin_tarihi'] . "</div>";
            echo "</div>";
        }
    } else {
        echo "<div class='empty-state'><p>Bu kulübe ait yayınlanmış duyuru bulunmuyor.</p></div>";
    }
    ?>
</div>

</body>
</html>