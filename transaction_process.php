<?php
session_start();
include 'db_config.php';
$notification = null;
if (isset($_POST['chuyentien'])) {
    $ngan_hang_nhan = $_POST['phan-loai'];
    $tai_khoan = $_POST['taikhoan'];
    $ten_chu_tk = $_POST['tenchutk'];
    $so_tien = str_replace(',', '', $_POST['sotien']);
    $noi_dung = $_POST['noidung'];
    $tai_khoan_goc = $_POST['taikhoanGoc'];
    mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);
    try {
        $DeleteMoneyTKGoc = "UPDATE data_user SET money = money - $so_tien WHERE stk = '$tai_khoan_goc' AND money >= $so_tien";
        $result = mysqli_query($con, $DeleteMoneyTKGoc);
        if (mysqli_affected_rows($con) == 0) {
            throw new Exception("Số dư không đủ.");
        }
        $Data_Acc_Nhan = mysqli_query($con, "SELECT * FROM data_user WHERE stk = '$tai_khoan'");
        if (!$Data_Acc_Nhan) {
            throw new Exception("Lỗi khi truy vấn tài khoản nhận: " . mysqli_error($con));
        }
        mysqli_query($con,"SAVEPOINT GhigiaodichChuyen");
        $Row_Data_Acc_Nhan = mysqli_fetch_assoc($Data_Acc_Nhan); 
        
        if (!DataTransactionRetry($con, $_SESSION['users'], $tai_khoan_goc, $tai_khoan, $so_tien, $noi_dung, 'Chuyển tiền', 'Thành công')) {
            mysqli_query($con,"ROLLBACK TO GhigiaodichChuyen");
            if (!DataTransactionRetry($con, $_SESSION['users'], $tai_khoan_goc, $tai_khoan, $so_tien, $noi_dung, 'Chuyển tiền', 'Thành công')) {
                throw new Exception("Ghi giao dịch chuyển tiền thất bại.");
            }
        }
        if (!AddMoneyAccNhan($con, $tai_khoan, $Row_Data_Acc_Nhan['money'], $so_tien)) {
            throw new Exception("Lỗi khi cộng tiền tài khoản nhận.");
        }
        mysqli_query($con,"SAVEPOINT GhigiaodichNhan");
        if (!DataTransactionRetry($con, $Row_Data_Acc_Nhan['user_acc'], $tai_khoan, $tai_khoan_goc, $so_tien, $noi_dung, 'Nhận tiền', 'Thành công')) {
            mysqli_query($con,"ROLLBACK TO GhigiaodichNhan");
            if (!DataTransactionRetry($con, $Row_Data_Acc_Nhan['user_acc'], $tai_khoan, $tai_khoan_goc, $so_tien, $noi_dung, 'Nhận tiền', 'Thành công')) {
                throw new Exception("Ghi giao dịch nhận tiền thất bại.");
            }
        }
        $con->commit();
        $notification = ['type' => 'success', 'message' => 'Giao dịch thành công!'];
    } catch (Exception $e) {
        $con->rollback();
        $notification = ['type' => 'error', 'message' => 'Giao dịch thất bại: ' . $e->getMessage()];
    }
}
function DataTransactionRetry($con, $user, $stk_source, $stk_taget, $money, $noi_dung, $type, $status, $retry_limit = 3){
    $retry_count = 0;
    while($retry_count < $retry_limit){
        if(DataTransaction($con, $user, $stk_source, $stk_taget, $money, $noi_dung, $type, $status)){
            return true;
        }
        $retry_count++;
        sleep(1);
    }
    return false;
}
function DataTransaction($con, $user, $stk_source, $stk_taget, $money, $noi_dung, $type, $status) {
    $ma_giao_dich = rand(100000, 999999);
    $query = "INSERT INTO transancition(ma_giao_dich, user_acc, stk_source, stk_taget, Money, noi_dung, Type, Status, Create_day)
              VALUES ('$ma_giao_dich', '$user', '$stk_source', '$stk_taget', '$money', '$noi_dung', '$type', '$status', NOW())";
    $AddData = mysqli_query($con, $query);
    if ($AddData) {
        return true;
    } else {
        return false;
    }
}
function AddMoneyAccNhan($con, $taikhoan_taget, $moneyGoc, $money) {
    $AddMoneyAccTaget = mysqli_query($con, "UPDATE data_user SET money = money + $money WHERE stk = '$taikhoan_taget'");
    if ($AddMoneyAccTaget) {
        return true;
    } else {
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo giao dịch</title>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #eef2f7;
}

.notification {
    width: 400px;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    z-index: 1000;
    opacity: 0;
    background: linear-gradient(135deg, #fff, #f9fafb);
    transition: transform 0.5s ease, opacity 0.5s ease, box-shadow 0.5s ease;
}

.notification.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

.notification.success {
    background-color: #e7f9ef;
    color: #28a745;
    border: 1px solid #d4edda;
}

.notification.error {
    background-color: #fbe2e5;
    color: #dc3545;
    border: 1px solid #f8d7da;
}

.notification i {
    font-size: 50px;
    margin-bottom: 20px;
}

.notification p {
    font-size: 16px;
    margin: 0;
    line-height: 1.5;
    color: #333;
}

.notification button {
    margin-top: 25px;
    padding: 12px 30px;
    background-color: #007bff;
    border: none;
    border-radius: 50px;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 3px 8px rgba(0, 123, 255, 0.3);
}

.notification button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.5);
}
.notification .home-btn {
    background-color: #28a745;
    margin-left: 10px;
    box-shadow: 0 3px 8px rgba(40, 167, 69, 0.3);
}

.notification .home-btn:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.5);
}

.notification.show {
    animation: fadeIn 0.4s ease forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}
    </style>
</head>
<body>
    <div id="notification" class="notification">
        <i id="icon"></i>
        <p id="message"></p>
        <button onclick="window.location.href='/chuyentien.php'">Đóng</button>
        <button class="home-btn" onclick="window.location.href='/bank.php'">Quay về trang chủ</button>
    </div>
    <script>
        function showNotification(type, message) {
            var notification = document.getElementById('notification');
            var icon = document.getElementById('icon');
            var msg = document.getElementById('message');
            msg.innerHTML = message;

            if (type === 'success') {
                notification.className = 'notification success show';
                icon.innerHTML = '✔️';
            } else if (type === 'error') {
                notification.className = 'notification error show';
                icon.innerHTML = '❌';
            }
            notification.style.display = 'block';
            setTimeout(closeNotification, 10000);
        }

        function closeNotification() {
            var notification = document.getElementById('notification');
            notification.classList.remove('show');
            setTimeout(function() {
                notification.style.display = 'none';
            }, 300);
        }
        <?php if ($notification): ?>
            window.onload = function() {
                showNotification('<?php echo $notification['type']; ?>', '<?php echo addslashes($notification['message']); ?>');
            };
        <?php endif; ?>
    </script>
</body>
</html>


