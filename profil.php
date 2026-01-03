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
$kullanici = $_SESSION['kullanici'];

// Kullanıcının fakülte adını veritabanından çekiyoruz
$fakulte_sorgu = $baglan->query("SELECT f.fakulte_adi FROM Kullanicilar u JOIN Fakulteler f ON u.fakulte_id = f.fakulte_id WHERE u.kullanici_id = $k_id");
$fakulte_verisi = $fakulte_sorgu->fetch_assoc();
$fakulte_adi = $fakulte_verisi['fakulte_adi'] ?? "Belirtilmemiş";

// --- SİLME VE İPTAL İŞLEMLERİ ---
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['iptal_et'])) {
        $etkinlik_id = (int)$_POST['etkinlik_id'];
        if($baglan->next_result()) $baglan->store_result();
        $baglan->query("CALL sp_KatilimIptalEt($k_id, $etkinlik_id)");
        header("Location: profil.php?mesaj=iptal");
    }
    
    if(isset($_POST['etkinlik_sil'])) {
        $e_id = (int)$_POST['e_id'];
        $baglan->query("DELETE FROM Etkinlikler WHERE etkinlik_id = $e_id");
        header("Location: profil.php?mesaj=silindi");
    }

    if(isset($_POST['duyuru_sil'])) {
        $d_id = (int)$_POST['d_id'];
        $baglan->query("DELETE FROM Duyurular WHERE duyuru_id = $d_id");
        header("Location: profil.php?mesaj=silindi");
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim - SocialUni</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        nav { background-color: #a33333; padding: 0 5%; height: 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .container { padding: 40px 5%; max-width: 1100px; margin: auto; }

        .profile-card { background: #1e1e1e; border-radius: 15px; border: 1px solid #333; padding: 30px; display: flex; align-items: center; gap: 30px; margin-bottom: 40px; }
        .profile-avatar { width: 80px; height: 80px; background: #a33333; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; color: white; border: 2px solid #f1c40f; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; background: #252525; color: #f1c40f; font-size: 11px; font-weight: bold; border: 1px solid #f1c40f; margin-bottom: 10px; }

        h3 { color: #ff4d4d; border-left: 5px solid #a33333; padding-left: 15px; margin: 40px 0 20px 0; }
        h4 { color: #bbb; margin: 25px 0 15px 15px; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; }
        
        .grid-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .history-card, .manage-card { background: #1e1e1e; border-radius: 12px; border: 1px solid #333; padding: 20px; border-left: 4px solid #a33333; display: flex; flex-direction: column; justify-content: space-between; }
        .manage-card { border-left-color: #555; }
        
        .history-card strong, .manage-card strong { display: block; color: #fff; font-size: 16px; margin-bottom: 8px; }
        .history-card span, .manage-card span { display: block; color: #888; font-size: 13px; margin-bottom: 4px; }

        .btn-action { background: transparent; border: 1px solid; padding: 8px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold; transition: 0.3s; margin-top: 15px; width: 100%; }
        .btn-red { border-color: #ff4d4d; color: #ff4d4d; }
        .btn-red:hover { background: #ff4d4d; color: white; }
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
    <div class="profile-card">
        <div class="profile-avatar"><?php echo strtoupper(substr($kullanici['ad_soyad'], 0, 1)); ?></div>
        <div class="profile-details">
            <div class="role-badge"><?php echo ($rol_id == 1) ? "🛡️ ADMİN" : (($rol_id == 2) ? "👑 BAŞKAN" : "🎓 ÖĞRENCİ"); ?></div>
            <h2><?php echo $kullanici['ad_soyad']; ?></h2>
            <p>🎓 <strong>Bölüm/Fakülte:</strong> <?php echo $fakulte_adi; ?></p>
            <p>📧 <strong>E-Posta:</strong> <?php echo $kullanici['email']; ?></p>
        </div>
    </div>

    <h3>🕒 Aktif Katılımlarım</h3>
    <div class="grid-list">
        <?php
        $katilimlar = $baglan->query("SELECT e.etkinlik_id, e.baslik, c.kulup_adi, kat.kayit_tarihi FROM Katilimlar kat JOIN Etkinlikler e ON kat.etkinlik_id = e.etkinlik_id JOIN Kulupler c ON e.kulup_id = c.kulup_id WHERE kat.kullanici_id = $k_id AND kat.durum = 'Onayli'");
        if ($katilimlar->num_rows > 0) {
            while($k = $katilimlar->fetch_assoc()){
                echo "<div class='history-card'>
                        <div><strong>{$k['baslik']}</strong><span>🏫 {$k['kulup_adi']}</span><span>🗓️ {$k['kayit_tarihi']}</span></div>
                        <form method='POST' onsubmit='return confirm(\"İptal edilsin mi?\");'>
                            <input type='hidden' name='etkinlik_id' value='{$k['etkinlik_id']}'>
                            <button type='submit' name='iptal_et' class='btn-action btn-red'>KATILIMI İPTAL ET</button>
                        </form>
                      </div>";
            }
        } else { echo "<p style='margin-left:15px; color:#666;'>Henüz bir etkinliğe katılmadınız.</p>"; }
        ?>
    </div>

    <?php if($rol_id <= 2): ?>
        <h3>🛠️ İçerik Yönetimi</h3>
        
        <h4>📅 Yayınladığım Etkinlikler</h4>
        <div class="grid-list">
            <?php
            $where = ($rol_id == 1) ? "" : "WHERE k.baskan_id = $k_id";
            $e_yayin = $baglan->query("SELECT e.etkinlik_id, e.baslik, k.kulup_adi FROM Etkinlikler e JOIN Kulupler k ON e.kulup_id = k.kulup_id $where");
            
            if ($e_yayin->num_rows > 0) {
                while($ey = $e_yayin->fetch_assoc()){
                    echo "<div class='manage-card'>
                            <div><strong>{$ey['baslik']}</strong><span>🏛️ {$ey['kulup_adi']}</span></div>
                            <form method='POST' onsubmit='return confirm(\"Bu etkinliği sistemden kalıcı olarak silmek istediğinize emin misiniz?\");'>
                                <input type='hidden' name='e_id' value='{$ey['etkinlik_id']}'>
                                <button type='submit' name='etkinlik_sil' class='btn-action btn-red'>ETKİNLİĞİ SİL</button>
                            </form>
                          </div>";
                }
            } else { echo "<p style='margin-left:15px; color:#666;'>Yayınlanmış etkinlik bulunamadı.</p>"; }
            ?>
        </div>

        <h4>📢 Yayınladığım Duyurular</h4>
        <div class="grid-list">
            <?php
            $d_yayin = $baglan->query("SELECT d.duyuru_id, d.baslik, k.kulup_adi FROM Duyurular d JOIN Kulupler k ON d.kulup_id = k.kulup_id ".($rol_id == 1 ? "" : "WHERE k.baskan_id = $k_id"));
            
            if ($d_yayin->num_rows > 0) {
                while($dy = $d_yayin->fetch_assoc()){
                    echo "<div class='manage-card'>
                            <div><strong>{$dy['baslik']}</strong><span>🏛️ {$dy['kulup_adi']}</span></div>
                            <form method='POST' onsubmit='return confirm(\"Bu duyuruyu silmek istediğinize emin misiniz?\");'>
                                <input type='hidden' name='d_id' value='{$dy['duyuru_id']}'>
                                <button type='submit' name='duyuru_sil' class='btn-action btn-red'>DUYURUYU SİL</button>
                            </form>
                          </div>";
                }
            } else { echo "<p style='margin-left:15px; color:#666;'>Yayınlanmış duyuru bulunamadı.</p>"; }
            ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>