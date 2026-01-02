<?php 
include 'baglan.php'; 
if(!isset($_SESSION['kullanici']) || $_SESSION['kullanici']['rol_id'] > 2) die("Yetkisiz erişim.");

if(isset($_POST['duyuru_yayinla'])){
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];
    $kulup_id = $_POST['kulup_id'];
    $tarih = date('Y-m-d'); // Güncel tarih 

    $sql = "INSERT INTO Duyurular (baslik, icerik, yayin_tarihi, kulup_id) 
            VALUES ('$baslik', '$icerik', '$tarih', $kulup_id)";
    
    if($baglan->query($sql)) {
        echo "<script>alert('Duyuru yayınlandı!'); window.location='duyurular.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Duyuru Ekle</title></head>
<body>
<nav><a href="anasayfa.php">🏠 Anasayfa</a></nav>
<div class="container">
    <div class="card">
        <h2>📢 Yeni Duyuru Yayınla</h2>
        <form method="POST">
            <input type="text" name="baslik" placeholder="Duyuru Başlığı" required style="width:100%; margin-bottom:10px; padding:8px;">
            <textarea name="icerik" placeholder="Duyuru İçeriği" required style="width:100%; height:120px; margin-bottom:10px; padding:8px;"></textarea>
            
            <label>Hangi Kulüp Adına:</label>
            <select name="kulup_id" style="width:100%; margin-bottom:10px; padding:8px;">
                <?php
                // Kullanıcı kulüp başkanıysa sadece kendi kulübünü görmesi sağlanabilir
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($k = $kulupler->fetch_assoc()) echo "<option value='{$k['kulup_id']}'>{$k['kulup_adi']}</option>";
                ?>
            </select>
            
            <button type="submit" name="duyuru_yayinla" class="btn" style="background:#2980b9;">Duyuruyu Paylaş</button>
        </form>
    </div>
</div>
</body>
</html>