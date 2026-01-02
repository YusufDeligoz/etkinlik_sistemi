<?php 
include 'baglan.php'; // Veritabanı bağlantısı

// Giriş İşlemi Mantığı
if(isset($_POST['email']) && isset($_POST['sifre'])){
    $email = $_POST['email']; 
    $sifre = $_POST['sifre'];
    
    // SQL4 dosyasındaki örnek verilerle kontrol
    $sorgu = $baglan->query("SELECT * FROM Kullanicilar WHERE email='$email' AND sifre='$sifre'");
    
    if($sorgu->num_rows > 0){
        $_SESSION['kullanici'] = $sorgu->fetch_assoc();
        header("Location: anasayfa.php");
        exit();
    } else { 
        $hata = "❌ E-posta veya şifre hatalı!"; 
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İSTÜN - Etkinlik Yönetimi Giriş</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #121212;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 40px 20px;
            text-align: center;
        }
        .login-logo {
            font-size: 56px;
            font-weight: bold;
            color: #a33333; /* İSTÜN Kırmızısı */
            margin-bottom: 5px;
            font-family: 'Brush Script MT', cursive;
        }
        .login-subtitle {
            color: #ccc;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-size: 14px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 15px;
            color: white;
            box-sizing: border-box;
            outline: none;
        }
        input:focus { border-color: #a33333; }
        .btn-login {
            background-color: #a33333;
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover { background-color: #822929; }
        .error-msg {
            color: #ff4444;
            margin-bottom: 15px;
            font-size: 14px;
            background: rgba(255, 68, 68, 0.1);
            padding: 10px;
            border-radius: 8px;
        }
        /* Hızlı Giriş Bölümü */
        .test-section {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }
        .btn-test {
            background: #222;
            color: #888;
            border: 1px solid #333;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin: 3px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-test:hover { color: white; border-color: #666; }
    </style>
    <script>
        function doldur(email, sifre) {
            document.getElementById('email').value = email;
            document.getElementById('sifre').value = sifre;
        }
    </script>
</head>
<body>

<div class="login-box">
    <div class="login-logo"> SocialUni </div>
    <div class="login-subtitle">Üniversite Etkinlik Yönetim Sistemi</div>

    <?php if(isset($hata)) echo "<div class='error-msg'>$hata</div>"; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>E-posta Adresi</label>
            <input type="email" name="email" id="email" placeholder="email@istun.edu.tr" required>
        </div>

        <div class="input-group">
            <label>Şifre</label>
            <input type="password" name="sifre" id="sifre" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">Giriş Yap</button>
    </form>

    <div class="test-section">
        <p style="color: #666; font-size: 12px; margin-bottom: 10px;">Hızlı Test Girişleri</p>
        <button class="btn-test" onclick="doldur('admin@istun.edu.tr', 'admin')">🛡️ Admin</button>
        <button class="btn-test" onclick="doldur('yusuf@istun.edu.tr', '12345')">👑 Başkan</button>
        <button class="btn-test" onclick="doldur('ahmet@mail.com', 'pass1')">🎓 Öğrenci</button>
    </div>
</div>

</body>
</html>