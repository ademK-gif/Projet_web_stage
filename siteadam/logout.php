<?php
session_start();
session_destroy();
header('Location: /siteadam/login.php');
exit;
