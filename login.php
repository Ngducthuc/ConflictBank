
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Đăng nhập </title>
    <link rel="stylesheet" href="styleLogin.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<?php
include 'db_config.php';
session_start();
if(isset($_POST['submit'])){
    $user_name = $_POST['username'];
    $password = $_POST['password'];
    $returl = CheckLogin($con,$user_name,$password);
    if($returl){
        $_SESSION['users'] = $user_name;
        echo '<script type="text/javascript">
                $(document).ready(function() {
                Swal.fire({
                icon: "success",
                title: "Đăng nhập thành công"
                });});
                </script>';
        echo '<script type="text/javascript">
                setTimeout(function() {
                window.location.href = "../bank.php";
                }, 2000 );
                </script>';
    }else{
        echo '<script type="text/javascript">
                $(document).ready(function() {
                Swal.fire({
                icon: "warning",
                title: "Thông tin không hợp lệ",
                confirmButtonText: "OK"
                });});
                </script>';
    }
}
function CheckLogin($con,$user_name,$password){
    $Check_TK = mysqli_query($con,"SELECT * FROM users WHERE user_acc = '$user_name' AND password = '$password'");
    if(mysqli_num_rows($Check_TK) > 0){
        return true;
    }else{
        return false;
    }
}
?>
<body>
    <header>
    </header>

    <main class="container">
        <h2> Đăng Nhập Hệ Thống </h2>
        <form method="post">
            <div class="input-field">
                <input type="text" name="username" placeholder="Nhập mã số tài khoản" required>
            </div>
            <div class="input-field">
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <input type="submit" name="submit" value="ĐĂNG NHẬP">
        </form>
        <div class="footer">
            <h3> Quên mật khẩu </h3>
        </div>
    </main>
</body>

</html>