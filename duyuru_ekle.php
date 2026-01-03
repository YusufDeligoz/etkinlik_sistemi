<?php 
ob_start();
session_start();
include 'baglan.php'; 

// Yetki Kontrolü: Sadece Admin (1) ve Kulüp Başkanı (2) erişebilir
if(!isset($_SESSION['kullanici']) || (int)$_SESSION['kullanici']['rol_id'] > 2) {
    die("<div style='background:#121212; color:#ff4d4d; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>
            <h2>🚫 Bu sayfaya erişim yetkiniz yok.</h2>
         </div>");
}

$rol_id = $_SESSION['kullanici']['rol_id'];

if(isset($_POST['duyuru_yayinla'])){
    $baslik = $baglan->real_escape_string($_POST['baslik']);
    $icerik = $baglan->real_escape_string($_POST['icerik']);
    $kulup_id = (int)$_POST['kulup_id'];
    $tarih = date('Y-m-d H:i:s'); 

    $sql = "INSERT INTO Duyurular (baslik, icerik, yayin_tarihi, kulup_id) 
            VALUES ('$baslik', '$icerik', '$tarih', $kulup_id)";
    
    if($baglan->query($sql)) {
        echo "<script>alert('Duyuru başarıyla yayınlandı!'); window.location='duyurular.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyuru Ekle - SocialUni</title>
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
        .nav-links a:hover { opacity: 0.7; }
        .logo { color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: 1px; }

        .container { padding: 40px 5%; max-width: 700px; margin: auto; }

        /* FORM KARTI */
        .form-card { 
            background: #1e1e1e; border-radius: 15px; border: 1px solid #333; 
            padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .form-card h2 { color: #ff4d4d; border-bottom: 1px solid #333; padding-bottom: 15px; margin-top: 0; margin-bottom: 25px; }

        /* INPUT STİLLERİ */
        label { display: block; margin-bottom: 8px; font-size: 13px; color: #888; text-transform: uppercase; font-weight: bold; }
        
        input[type="text"], select, textarea {
            width: 100%; padding: 12px; margin-bottom: 20px; 
            background: #252525; border: 1px solid #444; border-radius: 8px; 
            color: #fff; font-size: 15px; box-sizing: border-box; transition: 0.3s;
        }
        
        input:focus, select:focus, textarea:focus { border-color: #a33333; outline: none; background: #2a2a2a; }
        textarea { height: 150px; resize: vertical; }

        /* BUTON (İSTÜN KIRMIZISI) */
        .btn-paylas {
            background: #a33333; color: white; border: none; padding: 15px; 
            border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%;
            font-size: 16px; transition: 0.3s; margin-top: 10px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-paylas:hover { background: #c0392b; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(163, 51, 51, 0.3); }
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
    <div class="form-card">
        <h2>📢 Yeni Duyuru Yayınla</h2>
        <form method="POST">
            <label>Duyuru Başlığı</label>
            <input type="text" name="baslik" placeholder="Örn: Kulüp Toplantısı Hakkında" required>

            <label>Duyuru İçeriği</label>
            <textarea name="icerik" placeholder="Duyuru detaylarını buraya yazın..." required></textarea>

            <label>Yayınlayan Kulüp</label>
            <select name="kulup_id" required>
                <?php
                // Admin tüm kulupleri, Başkan ise sadece kendi kulübünü görebilir (Geliştirilebilir)
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($k = $kulupler->fetch_assoc()) {
                    echo "<option value='{$k['kulup_id']}'>{$k['kulup_adi']}</option>";
                }
                ?>
            </select>

            <button type="submit" name="duyuru_yayinla" class="btn-paylas">DUYURUYU ŞİMDİ PAYLAŞ</button>
        </form>
    </div>
</div>

</body>
</html>