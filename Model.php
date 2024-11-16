<?php
function CheckSTK($con,$user_name){
    $STK = mysqli_query($con,"SELECT * FROM data_user WHERE user_acc = '$user_name'");
    $row = mysqli_fetch_assoc($STK);
    return array(
        'stk' => $row['stk'],
        'money' => $row['money'],
        'name_bank' => $row['name_bank'],
        'ten_ctk' => $row['ten_ctk']
    );
}

?>