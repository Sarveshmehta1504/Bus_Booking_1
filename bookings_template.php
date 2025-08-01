<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusGo - My Bookings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="styles.css">
    <style>
        .bookings-container {
            margin-top: 80px;
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .user-welcome {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .user-welcome h2 {
            color: #2563eb;
            margin-bottom: 0.5rem;
        }

        .user-welcome p {
            color: #6b7280;
        }

        .bookings-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .bookings-header h1 {
            color: #2563eb;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .bookings-header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .bookings-content {
            display: grid;
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .booking-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid #3b82f6;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .booking-id {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .booking-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .booking-route {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .route-location {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .route-location .city {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
        }

        .route-location .time {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .route-arrow {
            color: #3b82f6;
            font-size: 1.5rem;
            margin: 0 1rem;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-item i {
            color: #3b82f6;
            width: 20px;
        }

        .detail-item span {
            font-weight: 500;
        }

        .booking-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #374151;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .booking-summary {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            text-align: center;
        }

        .stat-item {
            padding: 1rem;
            border-radius: 10px;
            background: #f8fafc;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .bookings-header h1 {
                font-size: 2rem;
            }

            .booking-route {
                flex-direction: column;
                text-align: center;
            }

            .route-arrow {
                transform: rotate(90deg);
                margin: 0.5rem 0;
            }

            .booking-actions {
                justify-content: center;
            }

            .btn {
                flex: 1;
                min-width: 120px;
                justify-content: center;
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
                    <?php if ($isLoggedIn): ?>
                        <a href="login.php?action=logout"><i class="fas fa-user"></i> Logout (<?= htmlspecialchars($username) ?>)</a>
                    <?php else: ?>
                        <a href="login.html"><i class="fas fa-user"></i> Login</a>
                    <?php endif; ?>
                    <a href="bookings.php" class="active"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Bookings Container -->
    <div class="bookings-container">
        <div class="container">
            <!-- User Welcome -->
            <?php if ($isLoggedIn): ?>
            <div class="user-welcome">
                <h2>Welcome back, <?= htmlspecialchars($username) ?>!</h2>
                <p>Here are all your bus bookings and travel history</p>
            </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($_GET['booking_success'])): ?>
            <div class="alert alert-success" style="background: #dcfce7; color: #166534; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem; border-left: 4px solid #16a34a;">
                <i class="fas fa-check-circle"></i>
                <strong>Booking Confirmed!</strong> Your bus ticket has been successfully booked. You can view and manage your booking below.
            </div>
            <?php endif; ?>

            <!-- Bookings Header -->
            <div class="bookings-header">
                <h1><i class="fas fa-ticket-alt"></i> My Bookings</h1>
                <p>Manage your bus reservations and travel history</p>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary">
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?= $totalBookings ?></div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= $upcomingTrips ?></div>
                        <div class="stat-label">Upcoming Trips</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">₹<?= number_format($totalSpent) ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                </div>
            </div>

            <!-- Bookings Content -->
            <div class="bookings-content">
                <?php if (empty($bookings)): ?>
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt"></i>
                        <h3>No Bookings Found</h3>
                        <p>You haven't made any bus reservations yet. Start your journey by booking your first trip!</p>
                        <a href="product.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Routes
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($bookings as $index => $booking): ?>
                        <?php
                            // Ensure all required keys have default values to prevent warnings
                            $id = $booking['id'] ?? 'Unknown';
                            $from = $booking['from'] ?? 'Unknown';
                            $to = $booking['to'] ?? 'Unknown';
                            $departure_date = $booking['departure_date'] ?? '1970-01-01';
                            $departure_time = $booking['departure_time'] ?? '06:00';
                            $arrival_time = $booking['arrival_time'] ?? 'N/A';
                            $bus_type = $booking['bus_type'] ?? 'Standard';
                            $price = $booking['price'] ?? 0;
                            $passengers = $booking['passengers'] ?? 1;
                            $status = $booking['status'] ?? 'confirmed';
                        ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-id">Booking ID: <?= htmlspecialchars($id) ?></div>
                                <div class="booking-status status-<?= htmlspecialchars($status) ?>">
                                    <?= ucfirst(htmlspecialchars($status)) ?>
                                </div>
                            </div>
                            
                            <div class="booking-route">
                                <div class="route-location">
                                    <div class="city"><?= htmlspecialchars($from) ?></div>
                                    <div class="time"><?= htmlspecialchars($departure_time) ?></div>
                                </div>
                                <div class="route-arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="route-location">
                                    <div class="city"><?= htmlspecialchars($to) ?></div>
                                    <div class="time"><?= htmlspecialchars($arrival_time) ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= date('M j, Y', strtotime($departure_date)) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?= $passengers ?> Passenger<?= $passengers > 1 ? 's' : '' ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-bus"></i>
                                    <span><?= htmlspecialchars($bus_type) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>₹<?= number_format($price) ?></span>
                                </div>
                                <?php 
                                $passengerName = $booking['passenger_name'] ?? '';
                                if (!empty($passengerName)): 
                                ?>
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span><?= htmlspecialchars($passengerName) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php 
                                $seatNumbers = $booking['seat_numbers'] ?? '';
                                if (!empty($seatNumbers)): 
                                ?>
                                <div class="detail-item">
                                    <i class="fas fa-chair"></i>
                                    <span>Seat: <?= htmlspecialchars($seatNumbers) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="booking-actions">
                                <button class="btn btn-primary" onclick="viewBookingDetails('<?= htmlspecialchars($id) ?>')">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <button class="btn btn-secondary" onclick="downloadTicket('<?= htmlspecialchars($id) ?>')">
                                    <i class="fas fa-download"></i> Download Ticket
                                </button>
                                <?php if ($status === 'confirmed'): ?>
                                    <button class="btn btn-danger" onclick="cancelBooking(<?= $index ?>)">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php endif; ?>
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
                    <div class="logo">
                        <i class="fas fa-bus"></i>
                        BusGo
                    </div>
                    <p>Your trusted partner for comfortable and safe bus travel across the country.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="product.php">Routes</a></li>
                        <li><a href="about.html">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="contact.html">Help Center</a></li>
                        <li><a href="bookings.php">My Bookings</a></li>
                        <li><a href="#cancellation">Cancellation Policy</a></li>
                        <li><a href="#terms">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Connect</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 BusGo. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        async function cancelBooking(index) {
            if (!confirm('Are you sure you want to cancel this booking?')) {
                return;
            }

            try {
                const response = await fetch('bookings.php?api=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'cancel',
                        index: index
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Booking cancelled successfully!');
                    location.reload(); // Reload the page to see updated status
                } else {
                    alert('Error cancelling booking: ' + data.error);
                }
            } catch (error) {
                console.error('Error cancelling booking:', error);
                alert('Error cancelling booking. Please try again.');
            }
        }

        function viewBookingDetails(bookingId) {
            alert(`Viewing details for booking ID: ${bookingId}\n\nThis feature will open a detailed view of your booking including seat numbers, pickup points, and contact information.`);
        }

        function downloadTicket(bookingId) {
            alert(`Downloading ticket for booking ID: ${bookingId}\n\nThis feature will generate and download your e-ticket as a PDF.`);
        }
    </script>
</body>
</html>
