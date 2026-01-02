<?php 
include 'baglan.php'; // Veritabanı bağlantısı
if(!isset($_SESSION['kullanici'])) header("Location: index.php"); 

$k_id = $_SESSION['kullanici']['kullanici_id'];
$rol_id = $_SESSION['kullanici']['rol_id']; // SQL4.sql'deki roller
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard - İSTÜN Etkinlik</title>
</head>
<body>

<nav>
    <a href="anasayfa.php" style="color: #a33333;">Anasayfa</a> 
    <a href="etkinlikler.php">Etkinlikler</a> 
    <a href="duyurular.php">Duyurular</a> 
    
    <?php if($rol_id <= 2): // Admin(1) veya Başkan(2) ise ?>
        <a href="etkinlik_ekle.php" style="color:#f1c40f;">➕ Etkinlik Ekle</a>
        <a href="duyuru_ekle.php" style="color:#f1c40f;">➕ Duyuru Ekle</a>
    <?php endif; ?>

    <a href="profil.php">Profilim</a> 
    <a href="cikis.php" style="color:#ff4444;">Çıkış</a>
</nav>

<div class="container">
    <h2>Hoş Geldiniz, <?php echo $_SESSION['kullanici']['ad_soyad']; ?></h2>
    
    <div class="card">
        <h3>🔔 Son Bildirimler</h3>
        <?php
        $bildirimler = $baglan->query("SELECT * FROM Bildirimler WHERE kullanici_id = $k_id ORDER BY tarih DESC LIMIT 3");
        if($bildirimler && $bildirimler->num_rows > 0){
            while($b = $bildirimler->fetch_assoc()){ 
                echo "<div style='margin-bottom:15px; padding-left:10px; border-left:2px solid #444;'>
                        <p style='font-size:14px;'>{$b['mesaj']}</p>
                        <small>🕒 {$b['tarih']}</small>
                      </div>"; 
            }
        } else { echo "<p style='color:#666;'>Henüz bildiriminiz yok.</p>"; }
        ?>
    </div>

    <div class="card">
        <h3>📊 Etkinlik Durumları</h3>
        <table>
            <thead>
                <tr>
                    <th>Etkinlik</th>
                    <th>Kategori</th>
                    <th>Kulüp</th>
                    <th>Kontenjan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $etkinlik_sorgu = "SELECT e.baslik, cat.kategori_adi, k.kulup_adi, e.mevcut_katilim, e.katilim_limiti 
                                   FROM Etkinlikler e
                                   JOIN Kulupler k ON e.kulup_id = k.kulup_id
                                   JOIN Kategoriler cat ON e.kategori_id = cat.kategori_id
                                   ORDER BY e.tarih DESC LIMIT 5";
                $res = $baglan->query($etkinlik_sorgu);
                while($row = $res->fetch_assoc()){
                    echo "<tr>
                            <td><strong>{$row['baslik']}</strong></td>
                            <td>{$row['kategori_adi']}</td>
                            <td>{$row['kulup_adi']}</td>
                            <td style='color:#a33333;'>{$row['mevcut_katilim']} / {$row['katilim_limiti']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php if($rol_id == 1): // ADMIN ÖZEL BÖLÜMÜ ?>
        
        <div class="card" style="border-top: 3px solid #a33333;">
            <h3>🛡️ Sistem Logları (Admin Only)</h3>
            <?php
            $loglar = $baglan->query("SELECT * FROM Sistem_Loglari ORDER BY islem_tarihi DESC LIMIT 4");
            while($l = $loglar->fetch_assoc()){
                echo "<p style='font-size:13px; margin-bottom:8px;'>
                        <span style='color:#666;'>[{$l['islem_tarihi']}]</span> 
                        <strong style='color:#ff4444;'>{$l['islem_tipi']}</strong>: {$l['detay']}
                      </p>";
            }
            ?>
        </div>

        <div class="card">
            <h3>🏫 Üniversite Genel Özeti</h3>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div>
                    <p style="color:#888; font-size:12px;">Yıllık Etkinlik Raporu (Procedure):</p>
                    <?php
                    if($baglan->next_result()) $baglan->store_result();
                    $rapor = $baglan->query("CALL sp_TarihAraligiRapor('2025-01-01', '2026-12-31')");
                    echo "<p style='font-size:24px; font-weight:bold;'>{$rapor->num_rows} <span style='font-size:14px; color:#666;'>Etkinlik</span></p>";
                    ?>
                </div>
                <div>
                    <p style="color:#888; font-size:12px;">Fakülte Katılımları:</p>
                    <?php
                    if($baglan->next_result()) $baglan->store_result();
                    $f_sorgu = "SELECT COUNT(*) as sayi FROM Katilimlar";
                    $f_res = $baglan->query($f_sorgu);
                    $f_data = $f_res->fetch_assoc();
                    echo "<p style='font-size:24px; font-weight:bold;'>{$f_data['sayi']} <span style='font-size:14px; color:#666;'>Toplam Kayıt</span></p>";
                    ?>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

</body>
</html>