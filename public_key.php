<?php
require_once 'config.php';
echo json_encode(['publicKey'=>config('public_key')]);