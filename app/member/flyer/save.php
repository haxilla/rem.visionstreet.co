<?php



echo "BEFORE REDIRECT<br>";

redirect('/member/flyer/details')->send();

echo "AFTER REDIRECT";
exit();