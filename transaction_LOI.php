<?php
session_start();
include 'db_config.php';
if(isset($_POST['chuyentien'])){
    $ngan_hang_nhan = $_POST['phan-loai'];
    $tai_khoan = $_POST['taikhoan'];
    $ten_chu_tk = $_POST['tenchutk'];
    $so_tien = str_replace(',', '', $_POST['sotien']);
    $noi_dung = $_POST['noidung'];
    $tai_khoan_goc = $_POST['taikhoanGoc'];
    $moneyGoc = $_POST['moneyGoc'];
   
    try {
        $con->query("LOCK TABLE data_user WRITE");
        $DeleteMoneyTKGoc = "UPDATE data_user SET money = $moneyGoc - $so_tien WHERE stk = '$tai_khoan_goc'";
        if ($con->query($DeleteMoneyTKGoc) === TRUE) {
            DataTransaction($con, $_SESSION['users'], $tai_khoan_goc, $tai_khoan, $so_tien, $noi_dung, 'Chuyển tiền', 'Thành công');
            $Data_Acc_Nhan = mysqli_query($con, "SELECT * FROM data_user WHERE stk = '$tai_khoan'");
            $Row_Data_Acc_Nhan = mysqli_fetch_assoc($Data_Acc_Nhan);
            $Data_Money_Goc_Acc_Taget = mysqli_query($con, "SELECT * FROM data_user WHERE stk = '$tai_khoan'");
            $money_Goc_Acc_Taget = mysqli_fetch_assoc($Data_Money_Goc_Acc_Taget);
            $resutlAddMoney = AddMoneyAccNhan($con, $tai_khoan, $money_Goc_Acc_Taget['money'], $so_tien);
            if ($resutlAddMoney) {
                DataTransaction($con, $Row_Data_Acc_Nhan['user_acc'], $tai_khoan, $tai_khoan_goc, $so_tien, $noi_dung, 'Nhận tiền', 'Thành công');
            }
        } else {
            echo 'Lỗi';
        }
        $con->query("UNLOCK TABLES");

    } catch (Exception $e) {
        echo "Giao dịch thất bại: " . $e->getMessage();
        $con->query("UNLOCK TABLES");
    }
}
function DataTransaction($con, $user, $stk_source, $stk_taget, $money, $noi_dung, $type, $status) {
    $ma_giao_dich = rand(0, 999999);
    $query = "INSERT INTO transancition(ma_giao_dich, user_acc, stk_source, stk_taget, Money, noi_dung, Type, Status, Create_day)
              VALUES ('$ma_giao_dich', '$user', '$stk_source', '$stk_taget', '$money', '$noi_dung', '$type', '$status', NOW())";
    $AddData = mysqli_query($con, $query);
    if ($AddData) {
        echo 'Thêm giao dịch thành công';
        return true;
    } else {
        echo 'Lỗi khi thêm giao dịch: ' . mysqli_error($con);
        return false;
    }
}
function AddMoneyAccNhan($con, $taikhoan_taget, $moneyGoc, $money) {
    $AddMoneyAccTaget = mysqli_query($con, "UPDATE data_user SET money = $moneyGoc + $money WHERE stk = '$taikhoan_taget'");
    if ($AddMoneyAccTaget) {
        echo 'Cộng tiền thành công';
        return true;
    } else {
        echo 'Tạch';
        return false;
    }
}
?>
