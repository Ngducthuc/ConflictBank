<?php
session_start();
$user_name = $_SESSION['users'];
include 'db_config.php';
$searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$sql = "SELECT * FROM transancition WHERE user_acc = '$user_name'";
if (!empty($searchKeyword)) {
    $sql .= " AND (ma_giao_dich LIKE '%$searchKeyword%' 
                   OR stk_taget LIKE '%$searchKeyword%' 
                   OR noi_dung LIKE '%$searchKeyword%' 
                   OR Type LIKE '%$searchKeyword%' 
                   OR Create_day LIKE '%$searchKeyword%')";
}
$Data_Transaction = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCB Digibank</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleLSgiaodich.css">
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
    <div class="main-content">
        <div class="header-right">
            <button>Cài đặt</button>
            <a href="logout.php"><button>Đăng xuất</button></a>
        </div>
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Nhập từ khóa tìm kiếm..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
            <button type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
        </form>
    </div>
    <table>
        <caption>Danh Sách Giao Dịch</caption>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã Giao Dịch</th>
                <th>Số Tài Khoản</th>
                <th>Số Tiền</th>
                <th>Nội Dung</th>
                <th>Loại Giao Dịch</th>
                <th>Ngày Giao Dịch</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stt = 1;
            while ($row_Data = mysqli_fetch_assoc($Data_Transaction)) {
                echo "<tr>";
                echo "<td>" . $stt . "</td>";
                echo "<td>" . $row_Data['ma_giao_dich'] . "</td>";
                echo "<td>" . $row_Data['stk_taget'] . "</td>";
                echo "<td>" . number_format($row_Data['Money'], 0, ',', '.') . " VND</td>";
                echo "<td>" . $row_Data['noi_dung'] . "</td>";
                echo "<td>" . $row_Data['Type'] . "</td>";
                echo "<td>" . $row_Data['Create_day'] . "</td>";
                echo "</tr>";
                $stt++;
            }
            ?>
        </tbody>
    </table>
</body>
</html>
