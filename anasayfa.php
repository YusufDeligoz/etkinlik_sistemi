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
$ad_soyad = $_SESSION['kullanici']['ad_soyad'];
$fakulte_id = $_SESSION['kullanici']['fakulte_id']; // Kullanıcının fakültesini alıyoruz

// --- İSTATİSTİKLERİ ÇEKELİM ---
$stats_etkinlik = $baglan->query("SELECT COUNT(*) as sayi FROM Etkinlikler")->fetch_assoc();
$stats_katilim = $baglan->query("SELECT COUNT(*) as sayi FROM Katilimlar")->fetch_assoc();
$stats_kulup = $baglan->query("SELECT COUNT(*) as sayi FROM Kulupler")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialUni - Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        nav { background-color: #a33333; padding: 0 5%; height: 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; transition: 0.3s; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .container { padding: 30px 5%; max-width: 1200px; margin: auto; }

        /* Dashboard Kartları */
        .card { background: #1e1e1e; padding: 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #333; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .card h3 { margin-top: 0; color: #ff4d4d; border-bottom: 1px solid #333; padding-bottom: 10px; }

        /* İstatistik Paneli Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
        .stat-item { background: #1e1e1e; padding: 20px; border-radius: 12px; border: 1px solid #333; text-align: center; border-bottom: 4px solid #a33333; }
        .stat-item span { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .stat-item h2 { margin: 10px 0 0 0; color: #fff; font-size: 32px; }

        /* İki Sütunlu Yapı */
        .dashboard-main { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        @media (max-width: 900px) { .dashboard-main { grid-template-columns: 1fr; } }

        .etkinlik-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .etkinlik-item { background: #252525; padding: 15px; border-radius: 10px; border-left: 4px solid #a33333; transition: 0.3s; }
        .etkinlik-item:hover { transform: translateY(-3px); background: #2a2a2a; }
        .etkinlik-item strong { display: block; font-size: 15px; color: #fff; margin-bottom: 5px; }
        .kontenjan { color: #f1c40f; font-weight: bold; display: block; margin-top: 8px; font-size: 11px; }
        
        .list-item { padding: 10px 0; border-bottom: 1px solid #333; font-size: 14px; }
        .list-item:last-child { border: none; }
        .badge { background: #a33333; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-right: 5px; }
    </style>
</head>
<body>

<nav>
    <div class="nav-links">
        <a href="anasayfa.php">Anasayfa</a> <a href="etkinlikler.php">Etkinlikler</a> <a href="duyurular.php">Duyurular</a> <a href="profil.php">Profilim</a> <a href="cikis.php" style="color:#ff6666;">Çıkış</a>
    </div>
    <div class="logo">SocialUni</div>
</nav>

<div class="container">
    <h2>Hoş Geldiniz, <?php echo $ad_soyad; ?> 👋</h2>

    <div class="stats-grid">
        <div class="stat-item"><span>Aktif Etkinlikler</span><h2><?php echo $stats_etkinlik['sayi']; ?></h2></div>
        <div class="stat-item"><span>Toplam Katılım</span><h2><?php echo $stats_katilim['sayi']; ?></h2></div>
        <div class="stat-item"><span>Aktif Kulüpler</span><h2><?php echo $stats_kulup['sayi']; ?></h2></div>
    </div>

    <div class="dashboard-main">
        <div class="content-left">
            <div class="card">
                <h3>📊 Güncel Etkinlikler</h3>
                <div class="etkinlik-grid">
                    <?php
                    $res = $baglan->query("SELECT e.baslik, cat.kategori_adi, k.kulup_adi, e.mevcut_katilim, e.katilim_limiti FROM Etkinlikler e JOIN Kulupler k ON e.kulup_id = k.kulup_id JOIN Kategoriler cat ON e.kategori_id = cat.kategori_id ORDER BY e.tarih DESC LIMIT 4");
                    while($row = $res->fetch_assoc()){
                        echo "<div class='etkinlik-item'><strong>{$row['baslik']}</strong><span>🏫 {$row['kulup_adi']}</span><br><span class='kontenjan'>DOLULUK: {$row['mevcut_katilim']} / {$row['katilim_limiti']}</span></div>";
                    }
                    ?>
                </div>
            </div>

            <div class="card">
                <h3>🔥 En Popüler Etkinlikler</h3>
                <?php
                $populer = $baglan->query("SELECT baslik, mevcut_katilim, katilim_limiti FROM Etkinlikler ORDER BY mevcut_katilim DESC LIMIT 3");
                while($p = $populer->fetch_assoc()){
                    $oran = round(($p['mevcut_katilim'] / $p['katilim_limiti']) * 100);
                    echo "<div class='list-item'><span class='badge'>POPÜLER</span> <b>{$p['baslik']}</b> - %$oran Doluluk oranına ulaştı!</div>";
                }
                ?>
            </div>
        </div>

        <div class="content-right">
            <div class="card">
                <h3>🔔 Bildirimler</h3>
                <?php
                $bildirimler = $baglan->query("SELECT * FROM Bildirimler WHERE kullanici_id = $k_id ORDER BY tarih DESC LIMIT 3");
                if($bildirimler->num_rows > 0){
                    while($b = $bildirimler->fetch_assoc()){ echo "<div class='list-item'><p style='margin:0;'>• {$b['mesaj']}</p><small style='color:#666;'>{$b['tarih']}</small></div>"; }
                } else { echo "<p style='color:#666; font-size:13px;'>Yeni bildirim yok.</p>"; }
                ?>
            </div>

            <div class="card">
                <h3>🎓 Fakültende Neler Oluyor?</h3>
                <?php
                // Kullanıcının fakültesindeki başkanların kulüplerinin etkinliklerini bulur
                $f_sorgu = "SELECT e.baslik FROM Etkinlikler e 
                            JOIN Kulupler k ON e.kulup_id = k.kulup_id 
                            JOIN Kullanicilar u ON k.baskan_id = u.kullanici_id 
                            WHERE u.fakulte_id = $fakulte_id LIMIT 3";
                $f_res = $baglan->query($f_sorgu);
                if($f_res->num_rows > 0){
                    while($fr = $f_res->fetch_assoc()){ echo "<div class='list-item'>📍 {$fr['baslik']}</div>"; }
                } else { echo "<p style='color:#666; font-size:13px;'>Fakültene özel etkinlik bulunamadı.</p>"; }
                ?>
            </div>
        </div>
    </div>

    <?php if($rol_id == 1): ?>
        <div class="card" style="border-top: 4px solid #ff4d4d;">
            <h3>🛡️ Admin Özet Paneli</h3>
            <div style="display:flex; gap:40px; align-items:center;">
                <div><span style="color:#888; font-size:12px;">Veritabanı Kayıt</span><p style="font-size:24px; font-weight:bold; margin:0;">
                <?php if($baglan->next_result()) $baglan->store_result(); echo $baglan->query("SELECT COUNT(*) as sayi FROM Katilimlar")->fetch_assoc()['sayi']; ?>
                </p></div>
                <div style="height:40px; width:1px; background:#333;"></div>
                <div><span style="color:#888; font-size:12px;">Sistem Durumu</span><p style="font-size:14px; font-weight:bold; margin:5px 0 0 0; color:#27ae60;">● ÇALIŞIYOR</p></div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>