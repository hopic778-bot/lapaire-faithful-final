<?php
/**
 * Cloudinary upload helper — no SDK needed, uses direct API call.
 */
define('CLOUDINARY_CLOUD', 'hu1u6d56');
define('CLOUDINARY_KEY',   '683856576864132');
define('CLOUDINARY_SECRET','Sa2nqslCg4wjQT5GRSm422zb0TU');

/**
 * Upload a file to Cloudinary.
 * @param string $filePath  — local tmp path ($_FILES[...]['tmp_name'])
 * @param string $folder    — e.g. 'lapaire/frames' or 'lapaire/blog'
 * @return array ['url'=>'https://...', 'public_id'=>'...'] on success
 * @throws RuntimeException on failure
 */
function cloudinary_upload(string $filePath, string $folder = 'lapaire/frames'): array {
    $timestamp  = time();
    $params     = ['folder' => $folder, 'timestamp' => $timestamp];
    ksort($params);

    // Build signature string
    $sigStr = '';
    foreach($params as $k => $v) $sigStr .= $k.'='.$v.'&';
    $sigStr  = rtrim($sigStr, '&') . CLOUDINARY_SECRET;
    $signature = sha1($sigStr);

    $url = 'https://api.cloudinary.com/v1_1/'.CLOUDINARY_CLOUD.'/image/upload';

    $postFields = [
        'file'      => new CURLFile($filePath),
        'folder'    => $folder,
        'timestamp' => $timestamp,
        'api_key'   => CLOUDINARY_KEY,
        'signature' => $signature,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_TIMEOUT        => 60,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if($curlErr) throw new RuntimeException('cURL error: '.$curlErr);

    $data = json_decode($response, true);

    if($httpCode !== 200 || empty($data['secure_url'])){
        $errMsg = $data['error']['message'] ?? $response;
        throw new RuntimeException('Cloudinary error ('.$httpCode.'): '.$errMsg);
    }

    return [
        'url'       => $data['secure_url'],
        'public_id' => $data['public_id'],
    ];
}

/**
 * Delete an image from Cloudinary by public_id.
 * Silent — won't throw on failure.
 */
function cloudinary_delete(string $publicId): void {
    if(empty($publicId)) return;
    $timestamp = time();
    $sigStr    = 'public_id='.$publicId.'&timestamp='.$timestamp.CLOUDINARY_SECRET;
    $signature = sha1($sigStr);

    $url = 'https://api.cloudinary.com/v1_1/'.CLOUDINARY_CLOUD.'/image/destroy';
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key'   => CLOUDINARY_KEY,
            'signature' => $signature,
        ],
        CURLOPT_TIMEOUT => 15,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

/**
 * Check if a string is a full URL (Cloudinary) or old local filename.
 */
function is_cloudinary_url(string $val): bool {
    return str_starts_with($val, 'http');
}
