<?php include 'baglan.php'; 
$e_id = $_GET['id'];
$k_id = $_SESSION['kullanici']['kullanici_id'];

if(isset($_POST['katil'])){
    $baglan->query("INSERT INTO Katilimlar (kullanici_id, etkinlik_id) VALUES ($k_id, $e_id)");
    echo "<script>alert('Kaydınız Alındı!'); window.location='detay.php?id=$e_id';</script>";
}
$e = $baglan->query("SELECT * FROM Etkinlikler WHERE etkinlik_id = $e_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title><?php echo $e['baslik']; ?></title></head>
<body>
<div class="container">
    <div class="card">
        <h1><?php echo $e['baslik']; ?></h1>
        <p><?php echo $e['aciklama']; ?></p>
        <p><b>Konum:</b> <?php echo $e['konum']; ?> | <b>Tarih:</b> <?php echo $e['tarih']; ?></p>
        <form method="POST"><button type="submit" name="katil" class="btn">Etkinliğe Katıl</button></form>
    </div>
    <div class="card">
        <h3>Yorumlar</h3>
        <?php
        $yorumlar = $baglan->query("SELECT y.*, u.ad_soyad FROM Yorumlar y JOIN Kullanicilar u ON y.kullanici_id = u.kullanici_id WHERE etkinlik_id = $e_id");
        while($y = $yorumlar->fetch_assoc()){ echo "<p><b>{$y['ad_soyad']}:</b> {$y['icerik']}</p>"; }
        ?>
    </div>
</div>
</body>
</html>