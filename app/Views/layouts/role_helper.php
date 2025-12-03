<?php
if (!function_exists('hasRole')) {
    function hasRole($role) {
        if (!isset($_SESSION['user']['roles'])) return false;
        return in_array($role, $_SESSION['user']['roles']);
    }
}
