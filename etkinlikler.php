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

// Filtreleme Parametreleri
$kat_filtre = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$kulup_filtre = isset($_GET['kulup']) ? (int)$_GET['kulup'] : 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etkinlikler - SocialUni</title>
    <style>
        /* İSTÜN STİLİ - TEMEL AYARLAR */
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        
        nav { 
            background-color: #a33333; padding: 0 5%; height: 60px;
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000;
        }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; transition: 0.3s; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }

        .container { padding: 30px 5%; max-width: 1200px; margin: auto; }
        
        .page-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 25px; border-left: 5px solid #a33333; padding-left: 15px;
        }
        .page-header h2 { margin: 0; color: #ffffff; }

        /* FİLTRELEME PANELİ */
        .filter-section {
            background: #1e1e1e; padding: 20px; border-radius: 12px; border: 1px solid #333; margin-bottom: 30px;
        }
        .category-filter { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #333; }
        .btn-kat { 
            background: #1e1e1e; color: #bbb; border: 1px solid #333; padding: 8px 16px; 
            border-radius: 20px; text-decoration: none; font-size: 13px; transition: 0.3s; 
        }
        .btn-kat:hover { border-color: #a33333; color: #fff; }
        .btn-kat.active { background: #a33333; color: #fff; border-color: #a33333; }

        .club-filter-form { display: flex; align-items: center; gap: 10px; }
        select { 
            background: #252525; color: white; border: 1px solid #444; padding: 10px; 
            border-radius: 8px; font-size: 14px; outline: none; flex-grow: 1;
        }
        .btn-filter { background: #444; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-filter:hover { background: #a33333; }

        .btn-aksiyon { background: #a33333; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; font-size: 13px; transition: 0.3s; }

        /* GRID VE KARTLAR */
        .etkinlik-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .etkinlik-card { background: #1e1e1e; border-radius: 12px; border: 1px solid #333; overflow: hidden; transition: 0.3s; display: flex; flex-direction: column; }
        .etkinlik-card:hover { transform: translateY(-8px); border-color: #a33333; box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
        
        .card-body { padding: 20px; }
        .card-body strong { display: block; font-size: 18px; color: #ff4d4d; margin-bottom: 10px; }
        .info-row { margin-bottom: 8px; font-size: 14px; color: #bbb; display: flex; align-items: center; gap: 8px; }
        
        .kontenjan-bar { background: #333; height: 8px; border-radius: 4px; margin: 15px 0 5px 0; overflow: hidden; }
        .kontenjan-fill { background: #a33333; height: 100%; }

        .btn-detay { display: block; text-align: center; background: #a33333; color: white; text-decoration: none; padding: 12px; font-weight: bold; transition: 0.3s; margin-top: auto; }
        .btn-detay:hover { background: #c0392b; }

        .label { color: #ffffff; font-size: 11px; background: #333; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; display: inline-block; }
        .empty-state { text-align: center; padding: 50px; color: #666; border: 1px dashed #444; border-radius: 12px; }
    </style>
</head>
<body>

<nav>
    <div class="nav-links">
        <a href="anasayfa.php">Anasayfa</a> 
        <a href="etkinlikler.php">Etkinlikler</a> 
        <a href="duyurular.php">Duyurular</a> 
        <a href="profil.php">Profilim</a> 
        <a href="cikis.php" style="color:#ff6666;">Çıkış</a>
    </div>
    <div class="logo">SocialUni</div>
</nav>

<div class="container">
    <div class="page-header">
        <h2>Tüm Etkinlikler</h2>
        <?php if((int)$rol_id <= 2): ?>
            <a href="etkinlik_ekle.php" class="btn-aksiyon">Yeni Etkinlik Oluştur</a>
        <?php endif; ?>
    </div>

    <div class="filter-section">
        <div class="category-filter">
            <a href="etkinlikler.php?kulup=<?php echo $kulup_filtre; ?>" class="btn-kat <?php echo ($kat_filtre == 0) ? 'active' : ''; ?>">Tümü</a>
            <?php
            $kategoriler = $baglan->query("SELECT * FROM Kategoriler");
            while($k = $kategoriler->fetch_assoc()){
                $active_class = ($kat_filtre == $k['kategori_id']) ? 'active' : '';
                echo "<a href='etkinlikler.php?kategori={$k['kategori_id']}&kulup={$kulup_filtre}' class='btn-kat {$active_class}'>{$k['kategori_adi']}</a>";
            }
            ?>
        </div>

        <form action="etkinlikler.php" method="GET" class="club-filter-form">
            <input type="hidden" name="kategori" value="<?php echo $kat_filtre; ?>">
            <select name="kulup">
                <option value="0">Tüm Kulüpler</option>
                <?php
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($klp = $kulupler->fetch_assoc()){
                    $sel = ($kulup_filtre == $klp['kulup_id']) ? 'selected' : '';
                    echo "<option value='{$klp['kulup_id']}' {$sel}>{$klp['kulup_adi']}</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-filter">Kulübe Göre Filtrele</button>
        </form>
    </div>
    
    <div class="etkinlik-grid">
        <?php
        // Dinamik SQL Sorgusu Oluşturma
        $sql = "SELECT e.*, k.kulup_adi, cat.kategori_adi FROM Etkinlikler e 
                JOIN Kulupler k ON e.kulup_id = k.kulup_id 
                JOIN Kategoriler cat ON e.kategori_id = cat.kategori_id";
        
        $filters = [];
        if($kat_filtre > 0) $filters[] = "e.kategori_id = $kat_filtre";
        if($kulup_filtre > 0) $filters[] = "e.kulup_id = $kulup_filtre";

        if(count($filters) > 0) {
            $sql .= " WHERE " . implode(" AND ", $filters);
        }
        
        $sql .= " ORDER BY e.tarih DESC";
        $sonuc = $baglan->query($sql);
        
        if($sonuc && $sonuc->num_rows > 0){
            while($satir = $sonuc->fetch_assoc()){
                $doluluk_orani = ($satir['mevcut_katilim'] / $satir['katilim_limiti']) * 100;
                ?>
                <div class="etkinlik-card">
                    <div class="card-body">
                        <span class="label"><?php echo $satir['kategori_adi']; ?></span>
                        <strong><?php echo $satir['baslik']; ?></strong>
                        <div class="info-row">🏛️ <span><?php echo $satir['kulup_adi']; ?></span></div>
                        <div class="info-row">📅 <span><?php echo $satir['tarih']; ?></span></div>
                        <div class="kontenjan-bar">
                            <div class="kontenjan-fill" style="width: <?php echo $doluluk_orani; ?>%;"></div>
                        </div>
                        <div style="font-size: 12px; color: #888; text-align: right;">
                            <?php echo $satir['mevcut_katilim']; ?> / <?php echo $satir['katilim_limiti']; ?> Katılımcı
                        </div>
                    </div>
                    <a href="detay.php?id=<?php echo $satir['etkinlik_id']; ?>" class="btn-detay">Etkinliği İncele</a>
                </div>
                <?php
            }
        } else {
            echo "<div class='empty-state'><p>Seçilen kriterlere uygun etkinlik bulunamadı.</p></div>";
        }
        ?>
    </div>
</div>

</body>
</html>