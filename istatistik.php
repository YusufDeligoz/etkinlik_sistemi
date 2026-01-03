<?php 
ob_start();
session_start();
include 'baglan.php'; 

// Yetki Kontrolü: Sadece Admin (rol_id = 1) girebilir
if(!isset($_SESSION['kullanici']) || $_SESSION['kullanici']['rol_id'] != 1) {
    die("<div style='background:#121212; color:#ff4d4d; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>
            <h2>⚠️ Bu raporlara sadece sistem yöneticileri erişebilir.</h2>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Analiz Raporları - SocialUni</title>
    <style>
        /* İSTÜN / SOCIALUNI TASARIM DİLİ */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        
        /* NAVBAR */
        nav { background-color: #a33333; padding: 0 5%; height: 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; transition: 0.3s; }
        .nav-links a:hover { opacity: 0.8; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }

        .container { padding: 40px 5%; max-width: 1200px; margin: auto; }
        
        /* BAŞLIKLAR */
        h1 { color: #ffffff; border-left: 5px solid #a33333; padding-left: 15px; margin-bottom: 30px; }
        .intro-text { color: #aaa; margin-bottom: 40px; font-size: 16px; }

        /* RAPOR KARTLARI */
        .report-section { background: #1e1e1e; margin-bottom: 50px; padding: 25px; border-radius: 15px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .report-section h2 { color: #ff4d4d; font-size: 20px; margin-top: 0; border-bottom: 1px solid #333; padding-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .report-section h2::before { content: '📊'; }

        /* TABLO TASARIMI */
        .table-responsive { overflow-x: auto; margin-top: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background-color: #a33333; color: white; padding: 15px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 12px 15px; border-bottom: 1px solid #2a2a2a; font-size: 14px; color: #ccc; }
        tr:nth-child(even) { background-color: #252525; }
        tr:hover { background-color: #2d2d2d; transition: 0.2s; }

        /* BOŞ DURUM MESAJI */
        .no-data { padding: 20px; text-align: center; color: #666; font-style: italic; }

        /* GERİ DÖNÜŞ LİNKİ */
        .back-link { display: inline-block; margin-top: 20px; color: #a33333; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .back-link:hover { color: #ff4d4d; text-decoration: underline; }
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
    <div class="logo">SocialUni <span style="font-size:12px; font-weight:normal; opacity:0.7;">Admin</span></div>

</nav>

<div class="container">
    <h1>📈 Sistem Analiz ve Sorgu Raporları</h1>
    <p class="intro-text">Aşağıdaki tablolar, veritabanı üzerindeki karmaşık JOIN işlemlerini içeren View yapılarının canlı verileridir. Bu raporlar sistemin genel işleyişini analiz etmek için kullanılır.</p>

    <?php
    $raporlar = [
        ["başlık" => "Etkinlik, Kulüp ve Kategori Analizi", "view" => "view_etkinlik_ozet"],
        ["başlık" => "Kullanıcı, Rol ve Fakülte Dağılımı", "view" => "view_kullanici_detay"],
        ["başlık" => "Detaylı Katılımcı Listesi", "view" => "view_katilim_analiz"],
        ["başlık" => "Kulüp Yönetim ve Fakülte İlişkisi", "view" => "view_kulup_yonetim"],
        ["başlık" => "Yorum ve Etkinlik Kategorisi Analizi", "view" => "view_yorum_detay"],
        ["başlık" => "Bildirim ve Kullanıcı Rol Analizi", "view" => "view_bildirim_rapor"],
        ["başlık" => "Duyuru ve Yayıncı Sorumluluk Listesi", "view" => "view_duyuru_kaynak"]
    ];

    foreach ($raporlar as $r) {
        echo "<div class='report-section'>";
        echo "<h2>{$r['başlık']}</h2>";
        
        // View var mı ve veri çekilebiliyor mu kontrolü
        $sonuc = $baglan->query("SELECT * FROM {$r['view']} LIMIT 5");
        
        if ($sonuc && $sonuc->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table><thead><tr>";
            $finfo = $sonuc->fetch_fields();
            foreach ($finfo as $val) { 
                echo "<th>" . str_replace('_', ' ', strtoupper($val->name)) . "</th>"; 
            }
            echo "</tr></thead><tbody>";
            while($row = $sonuc->fetch_assoc()) {
                echo "<tr>";
                foreach($row as $data) { 
                    echo "<td>" . htmlspecialchars($data) . "</td>"; 
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<div class='no-data'>Bu kategoriye ait gösterilecek veri bulunamadı veya View oluşturulmamış.</div>";
        }
        echo "</div>";
    }
    ?>

    <a href="anasayfa.php" class="back-link">← Yönetim Paneline Geri Dön</a>
</div>

</body>
</html>