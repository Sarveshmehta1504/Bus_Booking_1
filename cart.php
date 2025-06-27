<?php
session_start();
header('Content-Type: application/json');

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        // Return current bookings
        if (isset($_SESSION['bookings'])) {
            echo json_encode($_SESSION['bookings']);
        } else {
            echo json_encode([]);
        }
        break;
        
    case 'POST':
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'add':
                    // Add new booking
                    if (!isset($_SESSION['bookings'])) {
                        $_SESSION['bookings'] = [];
                    }
                    
                    $booking = [
                        'id' => uniqid(),
                        'from' => $input['from'] ?? '',
                        'to' => $input['to'] ?? '',
                        'departure_date' => $input['departure_date'] ?? '',
                        'departure_time' => $input['departure_time'] ?? '',
                        'arrival_time' => $input['arrival_time'] ?? '',
                        'passengers' => $input['passengers'] ?? 1,
                        'price' => $input['price'] ?? 0,
                        'bus_type' => $input['bus_type'] ?? 'Standard',
                        'booking_date' => date('Y-m-d H:i:s'),
                        'status' => 'confirmed'
                    ];
                    
                    $_SESSION['bookings'][] = $booking;
                    echo json_encode(['success' => true, 'booking' => $booking]);
                    break;
                    
                case 'cancel':
                    // Cancel booking
                    if (isset($_SESSION['bookings']) && isset($input['index'])) {
                        $index = intval($input['index']);
                        if (isset($_SESSION['bookings'][$index])) {
                            array_splice($_SESSION['bookings'], $index, 1);
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'error' => 'Booking not found']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Invalid request']);
                    }
                    break;
                    
                case 'clear':
                    // Clear all bookings
                    $_SESSION['bookings'] = [];
                    echo json_encode(['success' => true]);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'error' => 'Unknown action']);
                    break;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No action specified']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}