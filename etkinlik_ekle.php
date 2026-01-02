<?php 
include 'baglan.php'; 
// Sadece Admin (1) ve Kulüp Başkanı (2) erişebilir
if(!isset($_SESSION['kullanici']) || $_SESSION['kullanici']['rol_id'] > 2) {
    die("Bu sayfaya erişim yetkiniz yok.");
}

if(isset($_POST['etkinlik_kaydet'])){
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $tarih = $_POST['tarih'];
    $konum = $_POST['konum'];
    $limit = $_POST['katilim_limiti'];
    $kulup_id = $_POST['kulup_id'];
    $kategori_id = $_POST['kategori_id'];

    $sql = "INSERT INTO Etkinlikler (baslik, aciklama, tarih, konum, katilim_limiti, kulup_id, kategori_id) 
            VALUES ('$baslik', '$aciklama', '$tarih', '$konum', $limit, $kulup_id, $kategori_id)";
    
    if($baglan->query($sql)) {
        echo "<script>alert('Etkinlik başarıyla eklendi!'); window.location='etkinlikler.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Yeni Etkinlik</title></head>
<body>
<nav><a href="anasayfa.php">🏠 Anasayfa</a></nav>
<div class="container">
    <div class="card">
        <h2>📅 Yeni Etkinlik Oluştur</h2>
        <form method="POST">
            <input type="text" name="baslik" placeholder="Etkinlik Başlığı" required style="width:100%; margin-bottom:10px; padding:8px;">
            <textarea name="aciklama" placeholder="Etkinlik Açıklaması" style="width:100%; height:80px; margin-bottom:10px; padding:8px;"></textarea>
            <input type="datetime-local" name="tarih" required style="width:100%; margin-bottom:10px; padding:8px;">
            <input type="text" name="konum" placeholder="Etkinlik Konumu" style="width:100%; margin-bottom:10px; padding:8px;">
            <input type="number" name="katilim_limiti" placeholder="Katılımcı Limiti" value="100" style="width:100%; margin-bottom:10px; padding:8px;">
            
            <label>Düzenleyen Kulüp:</label>
            <select name="kulup_id" style="width:100%; margin-bottom:10px; padding:8px;">
                <?php
                $kulupler = $baglan->query("SELECT kulup_id, kulup_adi FROM Kulupler");
                while($k = $kulupler->fetch_assoc()) echo "<option value='{$k['kulup_id']}'>{$k['kulup_adi']}</option>";
                ?>
            </select>

            <label>Kategori:</label>
            <select name="kategori_id" style="width:100%; margin-bottom:10px; padding:8px;">
                <?php
                $kategoriler = $baglan->query("SELECT kategori_id, kategori_adi FROM Kategoriler");
                while($c = $kategoriler->fetch_assoc()) echo "<option value='{$c['kategori_id']}'>{$c['kategori_adi']}</option>";
                ?>
            </select>

            <button type="submit" name="etkinlik_kaydet" class="btn">Etkinliği Yayınla</button>
        </form>
    </div>
</div>
</body>
</html>