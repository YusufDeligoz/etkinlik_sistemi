<?php 
ob_start();
session_start();
include 'baglan.php'; 

if(!isset($_SESSION['kullanici'])) {
    header("Location: index.php");
    exit();
}

$e_id = (int)$_GET['id'];
$k_id = $_SESSION['kullanici']['kullanici_id'];
$rol_id = $_SESSION['kullanici']['rol_id'];

// --- KATILMA VE İPTAL ETME İŞLEMLERİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['katil'])) {
        // Daha önce iptal edilmiş bir kayıt var mı kontrol et
        $kontrol = $baglan->query("SELECT * FROM Katilimlar WHERE kullanici_id = $k_id AND etkinlik_id = $e_id");
        if ($kontrol->num_rows > 0) {
            // Varsa durumu tekrar 'Onayli' yap
            $baglan->query("UPDATE Katilimlar SET durum = 'Onayli' WHERE kullanici_id = $k_id AND etkinlik_id = $e_id");
        } else {
            // Yoksa yeni kayıt ekle (Trigger otomatik olarak mevcut_katilim'i artıracaktır)
            $baglan->query("INSERT INTO Katilimlar (kullanici_id, etkinlik_id) VALUES ($k_id, $e_id)");
        }
        echo "<script>alert('Kaydınız başarıyla alındı!'); window.location='detay.php?id=$e_id';</script>";
    }

    if (isset($_POST['iptal_et'])) {
        // Profil sayfasındaki mantıkla procedure'ü çağırıyoruz
        if($baglan->next_result()) $baglan->store_result();
        $baglan->query("CALL sp_KatilimIptalEt($k_id, $e_id)");
        echo "<script>alert('Katılımınız iptal edildi.'); window.location='detay.php?id=$e_id';</script>";
    }
}

// Kullanıcının katılım durumunu kontrol et
$durum_sorgu = $baglan->query("SELECT durum FROM Katilimlar WHERE kullanici_id = $k_id AND etkinlik_id = $e_id");
$katilim_durumu = ($durum_sorgu->num_rows > 0) ? $durum_sorgu->fetch_assoc()['durum'] : 'Kayitsiz';

// Etkinlik bilgilerini çek
$e_sorgu = $baglan->query("SELECT e.*, k.kulup_adi, c.kategori_adi 
                           FROM Etkinlikler e 
                           JOIN Kulupler k ON e.kulup_id = k.kulup_id 
                           JOIN Kategoriler c ON e.kategori_id = c.kategori_id 
                           WHERE e.etkinlik_id = $e_id");
$e = $e_sorgu->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $e['baslik']; ?> - SocialUni</title>
    <style>
        /* İSTÜN STİLİ TEMELLERİ */
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #121212; color: #e0e0e0; margin: 0; padding: 0; }
        nav { background-color: #a33333; padding: 0 5%; height: 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); position: sticky; top: 0; z-index: 1000; }
        .nav-links { display: flex; align-items: center; gap: 20px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-weight: 500; font-size: 14px; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }
        .container { padding: 40px 5%; max-width: 900px; margin: auto; }

        .event-main-card { background: #1e1e1e; border-radius: 15px; border: 1px solid #333; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .event-header { background: #a33333; padding: 30px; color: white; }
        .event-header h1 { margin: 0; font-size: 28px; }
        .event-content { padding: 30px; line-height: 1.6; }
        .event-info-box { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #252525; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .info-item b { color: #ff4d4d; display: block; font-size: 12px; text-transform: uppercase; }

        /* BUTONLAR */
        .btn-main { border: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; transition: 0.3s; margin-top: 25px; }
        .btn-katil { background: #a33333; color: white; }
        .btn-katil:hover { background: #c0392b; transform: scale(1.02); }
        .btn-iptal { background: transparent; border: 2px solid #ff4d4d; color: #ff4d4d; }
        .btn-iptal:hover { background: #ff4d4d; color: white; transform: scale(1.02); }

        .comments-section { background: #1e1e1e; border-radius: 15px; padding: 30px; border: 1px solid #333; }
        .comments-section h3 { color: #ff4d4d; border-bottom: 1px solid #333; padding-bottom: 15px; margin-top: 0; }
        .comment-bubble { background: #252525; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 3px solid #a33333; }
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
    <div class="event-main-card">
        <div class="event-header">
            <h1><?php echo htmlspecialchars($e['baslik']); ?></h1>
            <div class="meta"><span>📁 <?php echo htmlspecialchars($e['kategori_adi']); ?></span> | <span>🏫 <?php echo htmlspecialchars($e['kulup_adi']); ?></span></div>
        </div>
        
        <div class="event-content">
            <p style="font-size: 17px; color: #ddd;"><?php echo nl2br(htmlspecialchars($e['aciklama'])); ?></p>
            
            <div class="event-info-box">
                <div class="info-item"><b>ETKİNLİK KONUMU</b>📍 <?php echo htmlspecialchars($e['konum']); ?></div>
                <div class="info-item"><b>ETKİNLİK TARİHİ</b>📅 <?php echo $e['tarih']; ?></div>
                <div class="info-item"><b>KONTENJAN DURUMU</b>👥 <?php echo $e['mevcut_katilim']; ?> / <?php echo $e['katilim_limiti']; ?></div>
            </div>

            <form method="POST">
                <?php if ($katilim_durumu == 'Onayli'): ?>
                    <button type="submit" name="iptal_et" class="btn-main btn-iptal" onclick="return confirm('Katılımınızı iptal etmek istediğinize emin misiniz?');">KATILIMI İPTAL ET</button>
                <?php else: ?>
                    <button type="submit" name="katil" class="btn-main btn-katil">ETKİNLİĞE ŞİMDİ KATIL</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="comments-section">
        <h3>💬 Katılımcı Yorumları</h3>
        <?php
        $yorumlar = $baglan->query("SELECT y.*, u.ad_soyad FROM Yorumlar y JOIN Kullanicilar u ON y.kullanici_id = u.kullanici_id WHERE etkinlik_id = $e_id ORDER BY y.yorum_id DESC");
        if($yorumlar->num_rows > 0){
            while($y = $yorumlar->fetch_assoc()){ 
                echo "<div class='comment-bubble'><b>".htmlspecialchars($y['ad_soyad'])."</b><p>".htmlspecialchars($y['icerik'])."</p></div>"; 
            }
        } else { echo "<p style='color:#666; text-align:center;'>Henüz yorum yapılmamış. İlk yorumu sen yap!</p>"; }
        ?>
    </div>
</div>

</body>
</html>