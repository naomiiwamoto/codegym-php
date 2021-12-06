ご予約日：
<?php
foreach ($_POST['reserve'] as $reserve) {
    echo (htmlspecialchars($reserve, ENT_QUOTES) . ' ');
}
?>
