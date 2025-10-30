<?php
// Configuration for DB connection and app settings

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Adjust these for your local MySQL
const DB_HOST = 'localhost';
const DB_NAME = 'traction_ideas';
const DB_USER = 'root';
const DB_PASS = '';

// CSRF settings
const CSRF_TOKEN_KEY = '_csrf_token';

// App settings
const APP_NAME = 'Traction Ideas - Feedback Wall';

// Error reporting (development default)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set default timezone
date_default_timezone_set('UTC');
