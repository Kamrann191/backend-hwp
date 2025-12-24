<?php
declare(strict_types=1);

function initializeSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // SECURITY: ID anonymization
    if (!isset($_SESSION['id_map'])) {
        $_SESSION['id_map'] = [];        // Maps codes to real IDs
        $_SESSION['reverse_map'] = [];   // Maps real IDs to codes
        $_SESSION['session_start_time'] = time();
        $_SESSION['user_id'] = bin2hex(random_bytes(16)); // Anonymous user identifier
    }

    // Session timeout (1 hour)
    if (isset($_SESSION['session_start_time']) && (time() - $_SESSION['session_start_time'] > 3600)) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['id_map'] = [];
        $_SESSION['reverse_map'] = [];
        $_SESSION['session_start_time'] = time();
        $_SESSION['user_id'] = bin2hex(random_bytes(16));
    }
}

function anonymizeId(int $realId): string {
    if (isset($_SESSION['reverse_map'][$realId])) {
        return $_SESSION['reverse_map'][$realId];
    }
    
    do {
        $code = 'TRP_' . strtoupper(bin2hex(random_bytes(8)));
    } while (isset($_SESSION['id_map'][$code]));
    
    $_SESSION['id_map'][$code] = $realId;
    $_SESSION['reverse_map'][$realId] = $code;
    
    return $code;
}

function deanonymizeId(string $code): ?int {
    return $_SESSION['id_map'][$code] ?? null;
}

function cleanOldCodes(): void {
    if (count($_SESSION['id_map']) > 1000) {
        $_SESSION['id_map'] = array_slice($_SESSION['id_map'], -1000, null, true);
        $_SESSION['reverse_map'] = array_slice($_SESSION['reverse_map'], -1000, null, true);
    }
}
