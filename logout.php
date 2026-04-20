<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    logoutUser();
}

redirect('auth.php?logout=1');
