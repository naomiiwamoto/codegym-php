<?php
if (rand(0, 1) == 0) { //無造作な数字をとりだすにはrand　0または1を表示
    header('Location: a.html');
} else {
    header('Location: b.html');
}
