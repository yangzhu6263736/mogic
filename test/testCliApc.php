<?php
apcu_store("fuck", "ffsdfsdfsdf\n");
while (true) {
    sleep(1);
    echo apcu_fetch("fuck");
}
