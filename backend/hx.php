<?php

header('content-Type: application/json');
sleep(7);
echo json_encode(['success' => true, 'message' => 'HX endpoint reached successfully.']);