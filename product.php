<?php
session_start();

// Get search data from session
$search_data = $_SESSION['bus_search'] ?? null;

// Bus routes data - In a real application, this would come from a database
$all_bus_routes = [
    [
        "id" => 1,
        "route" => "Mumbai to Pune",
        "from" => "Mumbai",
        "to" => "Pune",
        "price" => 299,
        "duration" => "3h 30m",
        "image" => "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3",
        "operator" => "BusGo Express",
        "departure_times" => ["06:00", "10:30", "14:00", "18:30", "22:00"],
        "amenities" => ["WiFi", "AC", "Charging Port", "Water Bottle"],
        "seats_available" => 32,
        "rating" => 4.5,
        "distance" => "150 km"
    ],
    [
        "id" => 2,
        "route" => "Delhi to Agra",
        "from" => "Delhi",
        "to" => "Agra",
        "price" => 399,
        "duration" => "4h 15m",
        "image" => "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?ixlib=rb-4.0.3",
        "operator" => "BusGo Luxury",
        "departure_times" => ["07:00", "11:00", "15:30", "20:00"],
        "amenities" => ["WiFi", "AC", "Charging Port", "Snacks", "Blanket"],
        "seats_available" => 28,
        "rating" => 4.7,
        "distance" => "210 km"
    ],
    [
        "id" => 3,
        "route" => "Bangalore to Chennai",
        "from" => "Bangalore",
        "to" => "Chennai",
        "price" => 599,
        "duration" => "6h 45m",
        "image" => "https://images.unsplash.com/photo-1582510003544-4d00b7f74220?ixlib=rb-4.0.3",
        "operator" => "BusGo Premium",
        "departure_times" => ["08:00", "14:00", "20:30"],
        "amenities" => ["WiFi", "AC", "Charging Port", "Meals", "Entertainment"],
        "seats_available" => 24,
        "rating" => 4.8,
        "distance" => "350 km"
    ],
    [
        "id" => 4,
        "route" => "Kolkata to Bhubaneswar",
        "from" => "Kolkata",
        "to" => "Bhubaneswar",
        "price" => 450,
        "duration" => "5h 20m",
        "image" => "https://images.unsplash.com/photo-1591734942364-4c6e6f20c67e?ixlib=rb-4.0.3",
        "operator" => "BusGo Standard",
        "departure_times" => ["09:00", "16:00", "21:00"],
        "amenities" => ["WiFi", "AC", "Charging Port"],
        "seats_available" => 35,
        "rating" => 4.3,
        "distance" => "440 km"
    ],
    [
        "id" => 5,
        "route" => "Jaipur to Udaipur",
        "from" => "Jaipur",
        "to" => "Udaipur",
        "price" => 550,
        "duration" => "7h 10m",
        "image" => "https://images.unsplash.com/photo-1578894381394-076a6c07353b?ixlib=rb-4.0.3",
        "operator" => "BusGo Royal",
        "departure_times" => ["07:30", "19:00"],
        "amenities" => ["WiFi", "AC", "Charging Port", "Meals", "Pillow"],
        "seats_available" => 18,
        "rating" => 4.6,
        "distance" => "395 km"
    ],
    [
        "id" => 6,
        "route" => "Mumbai to Delhi",
        "from" => "Mumbai",
        "to" => "Delhi",
        "price" => 899,
        "duration" => "14h 30m",
        "image" => "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3",
        "operator" => "BusGo Sleeper",
        "departure_times" => ["19:00", "21:30"],
        "amenities" => ["WiFi", "AC", "Charging Port", "Meals", "Blanket", "Pillow"],
        "seats_available" => 22,
        "rating" => 4.4,
        "distance" => "1420 km"
    ]
];

// Filter routes based on search criteria
$filtered_routes = $all_bus_routes;

if ($search_data) {
    $filtered_routes = array_filter($all_bus_routes, function($route) use ($search_data) {
        $from_match = empty($search_data['from']) || 
                     stripos($route['from'], $search_data['from']) !== false;
        $to_match = empty($search_data['to']) || 
                   stripos($route['to'], $search_data['to']) !== false;
        
        return $from_match && $to_match;
    });
}

// Handle booking request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_bus'])) {
    $route_id = intval($_POST['route_id']);
    $departure_time = $_POST['departure_time'];
    $passengers = intval($_POST['passengers'] ?? 1);
    
    // Find the route
    $selected_route = null;
    foreach ($all_bus_routes as $route) {
        if ($route['id'] == $route_id) {
            $selected_route = $route;
            break;
        }
    }
    
    if ($selected_route) {
        // Create booking
        $booking = [
            'id' => 'BGO' . strtoupper(uniqid()),
            'from' => $selected_route['from'],
            'to' => $selected_route['to'],
            'departure_date' => $departure_date,
            'departure_time' => $departure_time,
            'arrival_time' => '14:30', // Calculate based on departure + duration
            'passengers' => $passengers,
            'price' => $selected_route['price'] * $passengers,
            'bus_type' => 'Standard',
            'booking_date' => date('Y-m-d H:i:s'),
            'status' => 'confirmed',
            'passenger_name' => $_SESSION['user']['name'] ?? 'Guest',
            'passenger_phone' => '',
            'seat_numbers' => '',
            'pickup_point' => ''
        ];
        
        // Store in session (in real app, save to database)
        if (!isset($_SESSION['bookings'])) {
            $_SESSION['bookings'] = [];
        }
        $_SESSION['bookings'][] = $booking;
        
        // Redirect to booking confirmation
        header('Location: bookings.php?booking_success=1');
        exit();
    }
}

// Get specific route if route_id is provided
$specific_route = null;
if (isset($_GET['route_id'])) {
    $route_id = intval($_GET['route_id']);
    foreach ($all_bus_routes as $route) {
        if ($route['id'] == $route_id) {
            $specific_route = $route;
            break;
        }
    }
}

// If it's an AJAX request, return JSON data
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_routes':
            echo json_encode($filtered_routes);
            break;
        case 'get_route':
            $route_id = intval($_GET['route_id'] ?? 0);
            $route = array_filter($all_bus_routes, function($r) use ($route_id) {
                return $r['id'] == $route_id;
            });
            echo json_encode(array_values($route)[0] ?? null);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Routes - BusGo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 2rem;
        }
        
        .nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav a:hover {
            color: #ffd700;
        }
        
        .content {
            margin-top: 80px;
            padding: 2rem 0;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }
        
        .search-summary {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .search-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .search-details {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .search-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .search-item i {
            color: #667eea;
        }
        
        .modify-search {
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .routes-grid {
            display: grid;
            gap: 2rem;
        }
        
        .route-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .route-card:hover {
            transform: translateY(-5px);
        }
        
        .route-header {
            display: flex;
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .route-image {
            width: 120px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 1.5rem;
        }
        
        .route-info {
            flex: 1;
        }
        
        .route-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .route-operator {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .route-meta {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .route-meta span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .route-details {
            padding: 1.5rem;
        }
        
        .departure-times {
            margin-bottom: 1rem;
        }
        
        .departure-times h4 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .time-slots {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .time-slot {
            background: #f8f9fa;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
            color: #333;
            border: 1px solid #e0e0e0;
        }
        
        .amenities {
            margin-bottom: 1rem;
        }
        
        .amenities h4 {
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .amenity-list {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .amenity {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .amenity i {
            color: #28a745;
        }
        
        .route-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
        }
        
        .price-info {
            display: flex;
            flex-direction: column;
        }
        
        .price {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .price-note {
            font-size: 0.8rem;
            color: #666;
        }
        
        .book-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
        }
        
        .no-routes {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .no-routes i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        
        .no-routes h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .no-routes p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h4 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #ffd700;
        }
        
        .footer-section p, .footer-section a {
            color: #ecf0f1;
            text-decoration: none;
            line-height: 1.8;
        }
        
        .footer-section a:hover {
            color: #ffd700;
        }
        
        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-icons a {
            background: #34495e;
            padding: 0.8rem;
            border-radius: 50%;
            transition: background 0.3s ease;
        }
        
        .social-icons a:hover {
            background: #667eea;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #34495e;
            color: #bdc3c7;
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .route-header {
                flex-direction: column;
            }
            
            .route-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .route-footer {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-bus"></i>
                    BusGo
                </div>
                <nav class="nav">
                    <a href="index.php">Home</a>
                    <a href="product.php">Routes</a>
                    <a href="about.html">About</a>
                    <a href="contact.php">Contact</a>
                    <a href="login.php"><i class="fas fa-user"></i> Login</a>
                    <a href="bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Available Bus Routes</h1>
                <p>Choose from our comfortable and reliable bus services</p>
            </div>

            <!-- Search Summary -->
            <?php if ($search_data): ?>
            <div class="search-summary">
                <div class="search-info">
                    <div class="search-details">
                        <?php if ($search_data['from']): ?>
                        <div class="search-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><strong>From:</strong> <?php echo htmlspecialchars($search_data['from']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($search_data['to']): ?>
                        <div class="search-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><strong>To:</strong> <?php echo htmlspecialchars($search_data['to']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($search_data['departure']): ?>
                        <div class="search-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> <?php echo date('M d, Y', strtotime($search_data['departure'])); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="search-item">
                            <i class="fas fa-users"></i>
                            <span><strong>Passengers:</strong> <?php echo $search_data['passengers']; ?></span>
                        </div>
                    </div>
                    <a href="index.php" class="modify-search">
                        <i class="fas fa-edit"></i> Modify Search
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Routes Grid -->
            <div class="routes-grid">
                <?php if (empty($filtered_routes)): ?>
                    <div class="no-routes">
                        <i class="fas fa-bus"></i>
                        <h3>No Routes Found</h3>
                        <p>Sorry, we couldn't find any bus routes matching your search criteria.</p>
                        <a href="index.php" class="book-btn">
                            <i class="fas fa-search"></i> Search Again
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($filtered_routes as $route): ?>
                    <div class="route-card">
                        <div class="route-header">
                            <img src="<?php echo htmlspecialchars($route['image']); ?>" alt="<?php echo htmlspecialchars($route['route']); ?>" class="route-image">
                            <div class="route-info">
                                <div class="route-title"><?php echo htmlspecialchars($route['route']); ?></div>
                                <div class="route-operator"><?php echo htmlspecialchars($route['operator']); ?></div>
                                <div class="route-meta">
                                    <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['duration']); ?></span>
                                    <span><i class="fas fa-road"></i> <?php echo htmlspecialchars($route['distance']); ?></span>
                                    <span><i class="fas fa-star"></i> <?php echo $route['rating']; ?></span>
                                    <span><i class="fas fa-users"></i> <?php echo $route['seats_available']; ?> seats left</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="route-details">
                            <div class="departure-times">
                                <h4>Departure Times</h4>
                                <div class="time-slots">
                                    <?php foreach ($route['departure_times'] as $time): ?>
                                        <span class="time-slot"><?php echo $time; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="amenities">
                                <h4>Amenities</h4>
                                <div class="amenity-list">
                                    <?php foreach ($route['amenities'] as $amenity): ?>
                                        <span class="amenity">
                                            <i class="fas fa-check"></i>
                                            <?php echo htmlspecialchars($amenity); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="route-footer">
                            <div class="price-info">
                                <span class="price">₹<?php echo number_format($route['price']); ?></span>
                                <span class="price-note">per person</span>
                            </div>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="route_id" value="<?php echo $route['id']; ?>">
                                <input type="hidden" name="departure_time" value="<?php echo $route['departure_times'][0]; ?>">
                                <input type="hidden" name="passengers" value="<?php echo $search_data['passengers'] ?? 1; ?>">
                                <button type="submit" name="book_bus" class="book-btn">
                                    <i class="fas fa-ticket-alt"></i> Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>BusGo</h4>
                    <p>Your trusted travel partner for comfortable and affordable bus journeys across the country.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <a href="index.php">Home</a>
                    <a href="product.php">Routes</a>
                    <a href="about.html">About Us</a>
                    <a href="contact.php">Contact</a>
                    <a href="#">Help & Support</a>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <a href="#">Bus Booking</a>
                    <a href="#">Route Planning</a>
                    <a href="#">Group Booking</a>
                    <a href="#">Corporate Services</a>
                    <a href="#">Travel Insurance</a>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-phone"></i> +91 1800-123-4567</p>
                    <p><i class="fas fa-envelope"></i> support@busgo.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Travel Street, Mumbai, India</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 BusGo. All rights reserved. | Privacy Policy | Terms & Conditions</p>
            </div>
        </div>
    </footer>
</body>
</html>
