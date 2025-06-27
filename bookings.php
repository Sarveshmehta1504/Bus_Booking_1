<?php
session_start();

// Set content type based on request
if (isset($_GET['api']) || $_SERVER['REQUEST_METHOD'] !== 'GET' || 
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
    header('Content-Type: application/json');
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

// Check if this is an AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

switch ($method) {
    case 'GET':
        if (isset($_GET['api']) || $isAjax) {
            // API request - return JSON
            handleApiRequest();
        } else {
            // Regular page request - return HTML with embedded data
            renderBookingsPage();
        }
        break;
        
    case 'POST':
        // Handle API actions
        handleApiRequest();
        break;
        
    default:
        if (isset($_GET['api'])) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        } else {
            http_response_code(405);
            echo "Method not allowed";
        }
        break;
}

function handleApiRequest() {
    global $method;
    
    if ($method === 'GET') {
        // Return current bookings
        $bookings = isset($_SESSION['bookings']) ? $_SESSION['bookings'] : [];
        echo json_encode([
            'success' => true,
            'bookings' => $bookings
        ]);
        return;
    }
    
    // Handle POST requests
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['action'])) {
        echo json_encode(['success' => false, 'error' => 'No action specified']);
        return;
    }
    
    switch ($input['action']) {
        case 'add':
            addBooking($input);
            break;
            
        case 'cancel':
            cancelBooking($input);
            break;
            
        case 'clear':
            clearBookings();
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
            break;
    }
}

function addBooking($input) {
    if (!isset($_SESSION['bookings'])) {
        $_SESSION['bookings'] = [];
    }
    
    // Validate required fields
    $requiredFields = ['from', 'to', 'departure_date', 'departure_time', 'price'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            $input[$field] = ''; // Default to empty string if missing
        }
    }
    
    // Ensure all required keys have default values to prevent warnings
    $booking = [
        'id' => 'BGO' . strtoupper(uniqid()),
        'from' => sanitize($input['from'] ?? 'Unknown'),
        'to' => sanitize($input['to'] ?? 'Unknown'),
        'departure_date' => sanitize($input['departure_date'] ?? '1970-01-01'),
        'departure_time' => sanitize($input['departure_time'] ?? '00:00'),
        'arrival_time' => sanitize($input['arrival_time'] ?? '00:00'),
        'passengers' => max(1, intval($input['passengers'] ?? 1)),
        'price' => max(0, floatval($input['price'] ?? 0)),
        'bus_type' => sanitize($input['bus_type'] ?? 'Standard'),
        'booking_date' => date('Y-m-d H:i:s'),
        'status' => 'confirmed',
        'passenger_name' => sanitize($input['passenger_name'] ?? 'Guest'),
        'passenger_phone' => sanitize($input['passenger_phone'] ?? 'Unknown'),
        'seat_numbers' => sanitize($input['seat_numbers'] ?? 'N/A'),
        'pickup_point' => sanitize($input['pickup_point'] ?? 'N/A')
    ];
    
    $_SESSION['bookings'][] = $booking;
    
    echo json_encode([
        'success' => true, 
        'booking' => $booking,
        'message' => 'Booking added successfully!'
    ]);
}

function cancelBooking($input) {
    if (!isset($_SESSION['bookings']) || !isset($input['index'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        return;
    }
    
    $index = intval($input['index']);
    
    if (!isset($_SESSION['bookings'][$index])) {
        echo json_encode(['success' => false, 'error' => 'Booking not found']);
        return;
    }
    
    // Mark as cancelled instead of deleting
    $_SESSION['bookings'][$index]['status'] = 'cancelled';
    $_SESSION['bookings'][$index]['cancelled_date'] = date('Y-m-d H:i:s');
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully!'
    ]);
}

function clearBookings() {
    $_SESSION['bookings'] = [];
    echo json_encode([
        'success' => true,
        'message' => 'All bookings cleared!'
    ]);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function renderBookingsPage() {
    // Get user info
    $isLoggedIn = isset($_SESSION['user']) && isset($_SESSION['user']['logged_in']) && $_SESSION['user']['logged_in'] === true;
    $username = $_SESSION['user']['name'] ?? 'Guest';
    $userEmail = $_SESSION['user']['email'] ?? '';
    
    // Get bookings
    $bookings = isset($_SESSION['bookings']) ? $_SESSION['bookings'] : [];
    
    // Calculate stats
    $totalBookings = count($bookings);
    $upcomingTrips = 0;
    $totalSpent = 0;
    
    foreach ($bookings as $booking) {
        // Ensure all required keys have default values to prevent warnings
        $booking['price'] = $booking['price'] ?? 0;
        $booking['departure_date'] = $booking['departure_date'] ?? '1970-01-01';

        if ($booking['status'] === 'confirmed' && strtotime($booking['departure_date']) > time()) {
            $upcomingTrips++;
        }
        if ($booking['status'] !== 'cancelled') {
            $totalSpent += floatval($booking['price']);
        }
    }
    
    // If user is not logged in and this is not an API request, redirect to login
    if (!$isLoggedIn && !isset($_GET['api']) && 
        !(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        header('Location: login.php?redirect=bookings.php');
        exit();
    }
    
    // For AJAX requests, return JSON instead of including template
    if (isset($_GET['api']) || 
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        echo json_encode([
            'success' => true,
            'bookings' => $bookings,
            'stats' => [
                'totalBookings' => $totalBookings,
                'upcomingTrips' => $upcomingTrips,
                'totalSpent' => $totalSpent
            ]
        ]);
        exit();
    }
    
    // Include the HTML template with PHP data for non-AJAX requests
    include 'bookings_template.php';
}

// If this file is accessed directly without any parameters, show the full page
if (!isset($_GET['api']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    renderBookingsPage();
}
?>
