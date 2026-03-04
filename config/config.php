<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const DB_HOST = 'localhost';
const DB_NAME = 'truck_marketplace';
const DB_USER = 'root';
const DB_PASS = '';
const BASE_URL = '/public';
const UPLOAD_PATH = __DIR__ . '/../uploads';
const MAX_IMAGES = 6;
const MAX_IMAGE_SIZE = 2 * 1024 * 1024;
const ALLOWED_IMAGE_MIME = ['image/jpeg', 'image/png', 'image/webp'];

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed. Please verify config/config.php settings.');
}
