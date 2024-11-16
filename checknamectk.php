<?php
include 'db_config.php';
if(isset($_POST['taikhoan'])){
    $taikhoan = $_POST['taikhoan'];
    $nganHang = $_POST['nganHang'];
    $Check = mysqli_query($con, "SELECT * FROM data_user WHERE stk = '$taikhoan' AND name_bank = '$nganHang'");
    if(mysqli_num_rows($Check) > 0){
        $row = mysqli_fetch_assoc($Check);
         echo $row['ten_ctk'];
    }else{
        echo 'Tài khoản hoặc ngân hàng không tồn tại';
    }
}
?>