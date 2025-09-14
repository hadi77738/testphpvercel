<?php
// Mulai session di baris paling atas
session_start();
 
// Jika pengguna sudah login, alihkan ke halaman admin
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: admin.php");
    exit;
}
 
// Sertakan file koneksi database
require_once "includes/db.php";
 
// Definisikan variabel dan inisialisasi dengan nilai kosong
$username = $password = "";
$login_err = "";
 
// Proses data form saat form disubmit
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Cek jika username kosong
    if(empty(trim($_POST["username"]))){
        $login_err = "Silakan masukkan username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Cek jika password kosong
    if(empty(trim($_POST["password"]))){
        $login_err = "Silakan masukkan password Anda.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validasi kredensial
    if(empty($login_err)){
        // Siapkan statement select
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variabel ke statement yang sudah disiapkan sebagai parameter
            $stmt->bind_param("s", $param_username);
            
            // Set parameter
            $param_username = $username;
            
            // Coba eksekusi statement
            if($stmt->execute()){
                // Simpan hasil
                $stmt->store_result();
                
                // Cek jika username ada, jika ya maka verifikasi password
                if($stmt->num_rows == 1){                    
                    // Bind hasil variabel
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password benar, mulai session baru
                            session_start();
                            
                            // Simpan data di session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Alihkan ke halaman admin
                            header("location: admin.php");
                        } else{
                            // Password tidak valid
                            $login_err = "Username atau password yang Anda masukkan salah.";
                        }
                    }
                } else{
                    // Username tidak ditemukan
                    $login_err = "Username atau password yang Anda masukkan salah.";
                }
            } else{
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }

            // Tutup statement
            $stmt->close();
        }
    }
    
    // Tutup koneksi
    $conn->close();
}

// Set judul halaman
$page_title = 'Login Admin';
// Sertakan header
require_once 'includes/header.php';
?>

<main>
    <section class="auth-section">
        <div class="auth-card">
            <h2>Login Admin</h2>
            <p>Silakan masukkan kredensial Anda untuk melanjutkan.</p>

            <?php 
            if(!empty($login_err)){
                echo '<div class="form-error-msg">' . htmlspecialchars($login_err) . '</div>';
            }        
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                </div>    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn-submit" value="Login">
                </div>
            </form>
        </div>
    </section>
</main>

<?php 
// Sertakan footer
require_once 'includes/footer.php'; 
?>
