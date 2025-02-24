<?php

function getDeviceName($userAgent) {
    if (preg_match('/iPhone|iPad|iPod/', $userAgent)) {
        return 'iOS Device';
    } elseif (preg_match('/Android/', $userAgent)) {
        return 'Android Device';
    } elseif (preg_match('/Windows/', $userAgent)) {
        return 'Windows PC';
    } elseif (preg_match('/Macintosh|Mac OS/', $userAgent)) {
        return 'Mac Device';
    } elseif (preg_match('/Linux/', $userAgent)) {
        return 'Linux PC';
    } else {
        return 'Unknown Device';
    }
}

function getUserLocation($ip) {
    $url = "http://ip-api.com/json/{$ip}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function logActivity($userId, $action) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $deviceName = getDeviceName($userAgent);
    $locationData = getUserLocation($ip);

    $country = $locationData['country'] ?? 'Unknown';
    $region = $locationData['regionName'] ?? 'Unknown';
    $city = $locationData['city'] ?? 'Unknown';
    $latitude = $locationData['lat'] ?? null;
    $longitude = $locationData['lon'] ?? null;

    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, ip_address, user_agent, device_name, country, region, city, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("issssssssd", $userId, $action, $ip, $userAgent, $deviceName, $country, $region, $city, $latitude, $longitude);
    $stmt->execute();
    $stmt->close();
}

?>

