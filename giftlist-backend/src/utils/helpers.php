<?php

function jsonResponse($success, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = ['success' => $success];
    
    if ($success) {
        $response['data'] = $data;
    } else {
        $response['error'] = $data;
    }
    
    echo json_encode($response);
}

function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

function getRequestUri() {
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

function parseRoute($pattern, $uri) {
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches);
        return $matches;
    }
    
    return false;
}
