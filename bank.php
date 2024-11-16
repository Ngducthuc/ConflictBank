<?php
session_start();
include 'db_config.php';
include 'Model.php';

?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCB Digibank</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleBank.css">
</head>

<body>
    <header>
        <div class="logo">
            <h2>VCB Digibank</h2>
        </div>
        <nav>
            <ul>
                <li><a href="bank.php">Trang chủ</a></li>
                <li><a href="chuyentien.php">Chuyển tiền</a></li>
                <li><a href="#">Nạp tiền</a></li>
                <li><a href="lsgiaodich.php">Lịch sử giao dịch</a></li>
            </ul>
        </nav>
        <div class="support-info">
            <p>Số điện thoại hỗ trợ: <span>1900 5454 13</span></p>
            <p>Chi nhánh gần nhất: <span>Hà Nội</span></p>
            <p>Yêu cầu hỗ trợ: <span>support@vcbdigibank.vn</span></p>
        </div>
    </header>
    <?php
    $user_Login = false;
    if(isset($_SESSION['users'])){
        $user_Login = true;
        $thongtin = CheckSTK($con, $_SESSION['users']);
    }
    ?>
    <div class="main-content">
        <div class="header-right">
            <?php if (isset($_SESSION['users'])){ echo '<p style="font-size: 20px;"><b>Xin chào, ' .$thongtin["ten_ctk"];} ?></b></p>
            <a href=" <?php if($user_Login){echo 'logout.php';}else{echo 'logout.php';} ?>"><button><?php if($user_Login){echo 'Đăng xuất';}else{echo 'Đăng nhập';} ?></button></a>
        </div>
<div class="account-info">
    <h3>Tài khoản mặc định</h3>
    <div class="details">
        <p>Số tài khoản: 
            <strong>
                <?php if (isset($_SESSION['users'])) {
                    echo $thongtin['stk'];
                } else {
                    echo 0;
                } ?>
            </strong>
        </p>
        <p>Số dư: 
            <strong>
                <span id="balance" class="hidden-balance">
                    **** VND
                </span>
                <span id="actual-balance" class="hidden">
                    <?php if (isset($_SESSION['users'])) {
                        echo number_format($thongtin['money']);
                    } else {
                        echo 0;
                    } ?> VND
                </span>
                <button id="toggle-balance" onclick="toggleBalance()">
                    <i id="icon-eye" class="fa fa-eye"></i>
                </button>
            </strong>
        </p>
    </div>
</div>
<script>
function toggleBalance() {
    const balanceSpan = document.getElementById('balance');
    const actualBalance = document.getElementById('actual-balance');
    const icon = document.getElementById('icon-eye');

    if (balanceSpan.classList.contains('hidden')) {
        balanceSpan.classList.remove('hidden');
        actualBalance.classList.add('hidden');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        balanceSpan.classList.add('hidden');
        actualBalance.classList.remove('hidden');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
        <div class="favorite-functions">
            <div>
                <i class="fas fa-hand-holding-heart"></i>
                <p>Chuyển tiền từ thiện</p>
            </div>
            <div>
                <a href="chuyentien.php" style="text-decoration: none;">
                <i class="fas fa-exchange-alt"></i>
                <p>Chuyển tiền trong nước</p>
                </a>
            </div>
            <div>
                <i class="fas fa-users"></i>
                <p>Quản lý nhóm</p>
            </div>
            <div>
                <i class="fas fa-piggy-bank"></i>
                <p>Mở tiết kiệm</p>
            </div>
            <div>
                <i class="fas fa-sliders-h"></i>
                <p>Cài đặt hạn mức chuyển tiền</p>
            </div>
            <div>
                <i class="fas fa-mobile-alt"></i>
                <p>Nạp tiền điện thoại</p>
            </div>
        </div>
        <div class="legal-links">
            <div class="links">
                <a href="#">Điều khoản sử dụng dịch vụ</a>
                <a href="#">Biểu phí dịch vụ</a>
                <a href="#">Hướng dẫn sử dụng dịch vụ</a>
                <a href="#">Hướng dẫn giao dịch an toàn</a>
            </div>
        </div>
    </div>
</body>
</html>

