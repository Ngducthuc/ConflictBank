<?php
session_start();
include 'db_config.php';
include 'Model.php';
$thongtin = CheckSTK($con,$_SESSION['users']);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCB Digibank</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleChuyenTien.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <!-- Chính -->
    <body>
        <div class="form-container">
            <h2>Chuyển tiền</h2>

            <!-- Thông tin tài khoản nguồn -->
            <div class="tknguon">
                <div class="form-group">
                    <b></b><label for="tai-khoan-nguon" style="color: black;">Tài khoản nguồn: <?php echo $thongtin['stk']; ?></label></b>
                </div>

                <div class="form-group">
                    <b></b><label for="so-du" style="color: black;">Số dư: <?php echo number_format($thongtin['money']); ?> VNĐ</label></b>
                </div>
            </div>

            <!-- Thông tin người nhận -->
            <h3>Thông tin người nhận</h3>
            <form id="transferForm" action="transaction_process.php" class="form-group" method="post">
                <label for="nhan-hang-nhan">Ngân hàng nhận</label>
                <select id="phan-loai" name="phan-loai" required>
                    <option value="VCB">Vietcombank</option>
                    <option value="MBBank">MB Bank</option>
                    <option value="VPB">VP Bank</option>
                    <option value="VTB">Vietinbank</option>
                    <option value="OCB">OCB</option>
                    <option value="BIDV">BIDV</option>
                    <option value="AGB">AGRIBANK</option>
                    <option value="ABBANK">ABBANK</option>
                    <option value="ACB">ACB</option>
                </select>
                <label for="taikhoan">Tài khoản/Thẻ nhận:</label>
                <input type="text" id="taikhoan" name="taikhoan" required>
                <label for="tenchutk">Tên chủ tài khoản</label>
                <input type="hidden" name="taikhoanGoc" value="<?php echo $thongtin['stk']; ?>">
                <input type="hidden" name="moneyGoc" value="<?php echo $thongtin['money']; ?>">
                <input type="text" id="tenchutk" name="tenchutk" required readonly>
                <h3>Thông tin giao dịch</h3>
                <div class="form-group">
                <label for="sotien">Số tiền</label>
                <input type="text" id="sotien" name="sotien" required>
                <p id="error-message" style="color:red; display:none;">Số tiền vượt quá số dư hiện có!</p>
                </div>
                <div class="form-group">
                    <label for="noidung">Nội dung</label>
                    <textarea id="noidung" name="noidung" rows="4" required><?php echo $thongtin['ten_ctk'] ?> chuyển tiền</textarea>
                </div>
                <button type="submit" name="chuyentien" class="submit-btn">Chuyển khoản</button>
            </form>
    </body>
</html>
<script>
    $(document).ready(function() {
        $('#taikhoan').on('blur', function() {
            var taikhoan = $(this).val();
            var nganHang = $('#phan-loai').val();
            if(taikhoan != '') {
                $.ajax({
                    url: 'checknamectk.php',
                    type: 'POST',
                    data: {
                        taikhoan: taikhoan,
                        nganHang: nganHang
                    },
                    success: function(response) {
                        if (response == 'Tài khoản hoặc ngân hàng không tồn tại') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Tài khoản hoặc ngân hàng không đúng'
                            });
                        } else {
                            $('#tenchutk').val(response);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại.'
                        });
                    }
                });
            }
        });
    });
    const sotienInput = document.getElementById('sotien');
    const errorMessage = document.getElementById('error-message');
    const maxMoney = <?php echo $thongtin['money']; ?>;
    let isAmountValid  = true;
    sotienInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/,/g, '');
        if (!/^\d+$/.test(value) && value !== '') {
            return;
        }
        e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const numericValue = Number(value);
        if (numericValue > maxMoney) {
            errorMessage.style.display = 'block';
            isAmountValid = false;
        } else {
            errorMessage.style.display = 'none';
            isAmountValid = true;
        }
    });
    transferForm.addEventListener('submit', function (e) {
        if (!isAmountValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Số tiền vượt quá số dư hiện có'
            });
        }
    });
</script>
</body>

</html>