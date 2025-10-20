<?php
// backend/api/ai/proxy.php
// Simple proxy to call OpenAI-compatible Chat Completions while hiding API key from the browser

header('Content-Type: application/json');

// CORS for dev
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Content-Type, Authorization');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  exit;
} else {
  header('Access-Control-Allow-Origin: *');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true) ?: [];

$model = $payload['model'] ?? 'gpt-3.5-turbo';
$messages = $payload['messages'] ?? [];
$temperature = $payload['temperature'] ?? 0.2;

// Load server-side env vars (edit to your server env management)
$apiKey = getenv('OPENAI_API_KEY') ?: '';
$apiBase = getenv('OPENAI_API_BASE') ?: 'https://api.openai.com';

if (!$apiKey) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Falta configurar la API Key del proveedor de IA en el servidor']);
  exit;
}

$body = [
  'model' => $model,
  'messages' => $messages,
  'temperature' => $temperature,
];

$ch = curl_init();
$url = rtrim($apiBase, '/') . '/v1/chat/completions';
curl_setopt_array($ch, [
  CURLOPT_URL => $url,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
  ],
  CURLOPT_POSTFIELDS => json_encode($body),
  CURLOPT_RETURNTRANSFER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($response === false) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error conectando al proveedor de IA: ' . $err]);
  exit;
}

if ($httpCode < 200 || $httpCode >= 300) {
  http_response_code($httpCode);
  echo json_encode(['success' => false, 'message' => 'IA error ' . $httpCode, 'raw' => $response]);
  exit;
}

$data = json_decode($response, true) ?: [];
$content = $data['choices'][0]['message']['content'] ?? '';

echo json_encode(['success' => true, 'content' => $content]);
