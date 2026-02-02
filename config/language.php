<?php
/**
 * Language Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language
define('DEFAULT_LANGUAGE', 'pt');

// Available languages
define('AVAILABLE_LANGUAGES', ['pt', 'en', 'es']);

/**
 * Set the current language
 */
function setLanguage($lang) {
    if (in_array($lang, AVAILABLE_LANGUAGES)) {
        $_SESSION['lang'] = $lang;
        return true;
    }
    return false;
}

// Check if language is specified in URL
if (isset($_GET['lang'])) {
    setLanguage($_GET['lang']);
    
    // Clean URL to remove lang parameter (optional, keeps URL clean)
    // $url = strtok($_SERVER["REQUEST_URI"], '?');
    // if (!empty($_GET)) {
    //     $params = $_GET;
    //     unset($params['lang']);
    //     if (!empty($params)) {
    //         $url .= '?' . http_build_query($params);
    //     }
    // }
    // header("Location: " . $url);
    // exit;
}

/**
 * Get the current language
 */
function getCurrentLanguage() {
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], AVAILABLE_LANGUAGES)) {
        return $_SESSION['lang'];
    }
    return DEFAULT_LANGUAGE;
}

/**
 * Load language translations
 */
function loadLanguage($lang) {
    $file = __DIR__ . '/lang/' . $lang . '.php';
    if (file_exists($file)) {
        return require $file;
    }
    // Fallback to default
    $defaultFile = __DIR__ . '/lang/' . DEFAULT_LANGUAGE . '.php';
    if (file_exists($defaultFile)) {
        return require $defaultFile;
    }
    return [];
}

// Global translations array
$langCode = getCurrentLanguage();
$translations = loadLanguage($langCode);

/**
 * Translate a key
 */
function t($key) {
    global $translations;
    
    // Support for nested keys (e.g., 'home.hero_title')
    $keys = explode('.', $key);
    $value = $translations;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $key; // Return key if translation not found
        }
    }
    
    if (is_array($value)) {
        return $key;
    }
    
    return $value;
}
?>
