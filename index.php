<?php
session_start();

// Handle bus search form submission
if ($_POST && isset($_POST['search_buses'])) {
    $search_data = [
        'from' => $_POST['from'] ?? '',
        'to' => $_POST['to'] ?? '',
        'departure' => $_POST['departure'] ?? '',
        'return' => $_POST['return'] ?? '',
        'passengers' => $_POST['passengers'] ?? 1
    ];
    
    // Store search data in session for use on results page
    $_SESSION['bus_search'] = $search_data;
    
    // Redirect to product.php (bus results page)
    header('Location: product.php');
    exit();
}

// Bus routes data - In a real application, this would come from a database
$bus_routes = [
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
        "amenities" => ["WiFi", "AC", "Charging Port", "Water Bottle"]
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
        "amenities" => ["WiFi", "AC", "Charging Port", "Snacks", "Blanket"]
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
        "amenities" => ["WiFi", "AC", "Charging Port", "Meals", "Entertainment"]
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
        "amenities" => ["WiFi", "AC", "Charging Port"]
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
        "amenities" => ["WiFi", "AC", "Charging Port", "Meals", "Pillow"]
    ]
];

// Featured routes for homepage (first 3)
$featured_routes = array_slice($bus_routes, 0, 3);

// Popular cities
$popular_cities = [
    "Mumbai", "Delhi", "Bangalore", "Chennai", "Kolkata", "Pune", 
    "Hyderabad", "Ahmedabad", "Jaipur", "Surat", "Lucknow", "Kanpur",
    "Nagpur", "Indore", "Agra", "Vadodara", "Ghaziabad", "Ludhiana"
];

// Get current date for form validation
$current_date = date('Y-m-d');

// If it's an AJAX request, return JSON data
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_routes':
            echo json_encode($bus_routes);
            break;
        case 'get_featured':
            echo json_encode($featured_routes);
            break;
        case 'get_cities':
            echo json_encode($popular_cities);
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
    <title>BusGo - Your Travel Partner</title>
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
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3') center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-top: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input, .form-group select {
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
        }
        
        .features {
            padding: 5rem 0;
            background: #f8f9fa;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #333;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-card i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .popular-routes {
            padding: 5rem 0;
        }
        
        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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
        
        .route-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .route-info {
            padding: 1.5rem;
        }
        
        .route-info h4 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .route-info .price {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .route-info .duration {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .book-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .book-btn:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 1rem;
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
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
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

    <!-- Hero Section with Booking Form -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Travel Comfortable, Travel Smart</h1>
                <p>Book your bus tickets online and enjoy a hassle-free journey</p>
                
                <div class="booking-form">
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="from">From</label>
                                <input type="text" id="from" name="from" placeholder="Enter departure city" required list="cities">
                                <datalist id="cities">
                                    <?php foreach ($popular_cities as $city): ?>
                                        <option value="<?php echo htmlspecialchars($city); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="to">To</label>
                                <input type="text" id="to" name="to" placeholder="Enter destination city" required list="cities">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="departure">Departure Date</label>
                                <input type="date" id="departure" name="departure" min="<?php echo $current_date; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="return">Return Date</label>
                                <input type="date" id="return" name="return" min="<?php echo $current_date; ?>">
                            </div>
                            <div class="form-group">
                                <label for="passengers">Passengers</label>
                                <select id="passengers" name="passengers" required>
                                    <option value="1">1 Passenger</option>
                                    <option value="2">2 Passengers</option>
                                    <option value="3">3 Passengers</option>
                                    <option value="4">4 Passengers</option>
                                    <option value="5">5+ Passengers</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="search_buses" class="search-btn">
                            <i class="fas fa-search"></i> Search Buses
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose BusGo?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Safe & Secure</h3>
                    <p>Your safety is our priority. All our buses are regularly maintained and drivers are well-trained professionals.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>On-Time Service</h3>
                    <p>We value your time. Our buses follow strict schedules to ensure you reach your destination on time.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Best Prices</h3>
                    <p>Get the best deals on bus tickets with our competitive pricing and exclusive offers.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-wifi"></i>
                    <h3>Modern Amenities</h3>
                    <p>Enjoy free WiFi, comfortable seating, air conditioning, and charging ports on all our buses.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Our customer support team is available round the clock to assist you with any queries or issues.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Easy Booking</h3>
                    <p>Book your tickets easily through our website or mobile app with just a few clicks.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Routes Section -->
    <section class="popular-routes">
        <div class="container">
            <h2 class="section-title">Popular Routes</h2>
            <div class="routes-grid">
                <?php foreach ($featured_routes as $route): ?>
                <div class="route-card">
                    <img src="<?php echo htmlspecialchars($route['image']); ?>" alt="<?php echo htmlspecialchars($route['route']); ?>">
                    <div class="route-info">
                        <h4><?php echo htmlspecialchars($route['route']); ?></h4>
                        <div class="price">Starting from ₹<?php echo number_format($route['price']); ?></div>
                        <div class="duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['duration']); ?></div>
                        <a href="product.php?route_id=<?php echo $route['id']; ?>" class="book-btn">Book Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

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

    <script>
        // Set minimum date for return date based on departure date
        document.getElementById('departure').addEventListener('change', function() {
            const departureDate = this.value;
            document.getElementById('return').setAttribute('min', departureDate);
        });
    </script>
</body>
</html>