<?php 
ob_start();
session_start();
include 'baglan.php'; 

// Sadece Admin (1) ve Kulüp Başkanı (2) erişebilir
if(!isset($_SESSION['kullanici']) || (int)$_SESSION['kullanici']['rol_id'] > 2) {
    die("<div style='background:#121212; color:#ff4d4d; height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif;'>
            <h2>🚫 Bu sayfaya erişim yetkiniz yok.</h2>
         </div>");
}

$rol_id = $_SESSION['kullanici']['rol_id'];

if(isset($_POST['etkinlik_kaydet'])){
    $baslik = $baglan->real_escape_string($_POST['baslik']);
    $aciklama = $baglan->real_escape_string($_POST['aciklama']);
    $tarih = $_POST['tarih'];
    $konum = $baglan->real_escape_string($_POST['konum']);
    $limit = (int)$_POST['katilim_limiti'];
    $kulup_id = (int)$_POST['kulup_id'];
    $kategori_id = (int)$_POST['kategori_id'];

    $sql = "INSERT INTO Etkinlikler (baslik, aciklama, tarih, konum, katilim_limiti, kulup_id, kategori_id) 
            VALUES ('$baslik', '$aciklama', '$tarih', '$konum', $limit, $kulup_id, $kategori_id)";
    
    if($baglan->query($sql)) {
        echo "<script>alert('Etkinlik başarıyla eklendi!'); window.location='etkinlikler.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Etkinlik - SocialUni</title>
    <style>
        /* GÜNCEL İSTÜN STİLİ (SARISIZ) */
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
        
        input[type="text"], input[type="number"], input[type="datetime-local"], select, textarea {
            width: 100%; padding: 12px; margin-bottom: 20px; 
            background: #252525; border: 1px solid #444; border-radius: 8px; 
            color: #fff; font-size: 15px; box-sizing: border-box; transition: 0.3s;
        }
        
        input:focus, select:focus, textarea:focus { border-color: #a33333; outline: none; background: #2a2a2a; }
        textarea { height: 120px; resize: vertical; }

        /* BUTON */
        .btn-yayinla {
            background: #a33333; color: white; border: none; padding: 15px; 
            border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%;
            font-size: 16px; transition: 0.3s; margin-top: 10px;
        }
        .btn-yayinla:hover { background: #c0392b; transform: translateY(-2px); }
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
        <h2>📅 Yeni Etkinlik Oluştur</h2>
        <form method="POST">
            <label>Etkinlik Başlığı</label>
            <input type="text" name="baslik" placeholder="Örn: Yapay Zeka Semineri" required>

            <label>Etkinlik Açıklaması</label>
            <textarea name="aciklama" placeholder="Etkinlik detaylarını buraya yazın..." required></textarea>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Tarih ve Saat</label>
                    <input type="datetime-local" name="tarih" required>
                </div>
                <div>
                    <label>Katılımcı Limiti</label>
                    <input type="number" name="katilim_limiti" value="100" min="1">
                </div>
            </div>

            <label>Etkinlik Konumu</label>
            <input type="text" name="konum" placeholder="Örn: Konferans Salonu A-1" required>

            <label>Düzenleyen Kulüp</label>
            <select name="kulup_id" required>
                <?php
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($k = $kulupler->fetch_assoc()) {
                    echo "<option value='{$k['kulup_id']}'>{$k['kulup_adi']}</option>";
                }
                ?>
            </select>

            <label>Kategori</label>
            <select name="kategori_id" required>
                <?php
                $kategoriler = $baglan->query("SELECT kategori_id, kategori_adi FROM Kategoriler");
                while($c = $kategoriler->fetch_assoc()) {
                    echo "<option value='{$c['kategori_id']}'>{$c['kategori_adi']}</option>";
                }
                ?>
            </select>

            <button type="submit" name="etkinlik_kaydet" class="btn-yayinla">ETKİNLİĞİ YAYINLA</button>
        </form>
    </div>
</div>

</body>
</html>