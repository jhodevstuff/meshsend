<?php
header('Content-Type: application/json');

$node = isset($_REQUEST['node']) ? trim($_REQUEST['node']) : '';
$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
$message = isset($_REQUEST['message']) ? trim($_REQUEST['message']) : '';

if (!$message) {
  http_response_code(400);
  echo json_encode(['error' => 'wtf what do you want']);
  exit;
}

if ($node !== '' && !preg_match('/^!?[a-zA-Z0-9]+$/', $node)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid node ID']);
  exit;
}

if ($name !== '' && strlen($name) > 30) {
  http_response_code(400);
  echo json_encode(['error' => 'I bet this is not your name bruh']);
  exit;
}

if (strlen($message) > 230) {
  http_response_code(400);
  echo json_encode(['error' => 'Message too long']);
  exit;
}

if ($name !== '') {
  $final_message = "Nachricht von " . $name . ": " . $message;
} else {
  $final_message = $message;
}

$escaped_message = escapeshellarg($final_message);
$scriptPath = '/var/www/meshsend/meshsend.sh';

if ($node !== '') {
  $escaped_node = escapeshellarg($node);
  $cmd = "$scriptPath $escaped_node $escaped_message > /dev/null 2>&1";
} else {
  $cmd = "$scriptPath " . escapeshellarg('__PUBLIC__') . " $escaped_message > /dev/null 2>&1";
}

exec($cmd, $output, $return_var);

if ($return_var !== 0) {
  http_response_code(500);
  echo json_encode(['error' => 'Command execution failed']);
  exit;
}

echo json_encode(['status' => 'Message sent successfully']);
?>