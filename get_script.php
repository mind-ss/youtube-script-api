<?php
header('Content-Type: application/json');

function getYouTubeCaptions($video_id) {
    $url = "https://www.youtube.com/watch?v=" . $video_id;
    $html = file_get_contents($url);

    // 자막 스크립트 추출
    preg_match('/({"captionTracks":.*?})/', $html, $matches);
    
    if (isset($matches[1])) {
        $json = json_decode($matches[1], true);
        if (isset($json['captionTracks']) && count($json['captionTracks']) > 0) {
            $caption_url = $json['captionTracks'][0]['baseUrl'];
            $caption_data = file_get_contents($caption_url);
            return $caption_data;
        }
    }
    
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['video_id'])) {
    $video_id = $_GET['video_id'];
    $script = getYouTubeCaptions($video_id);

    if ($script) {
        echo json_encode(['video_id' => $video_id, 'script' => $script]);
    } else {
        echo json_encode(['error' => '자막을 찾을 수 없습니다.']);
    }
} else {
    echo json_encode(['error' => '잘못된 요청입니다.']);
}
?>
