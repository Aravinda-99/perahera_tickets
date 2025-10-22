<?php
require 'db_connect.php';
require_once 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Perahera Tickets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* --- Navbar and Body Styles (පැරණි ඒවාමයි) --- */
        .navbar { 
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-color:rgb(56, 42, 24);
            overflow: hidden; 
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00bfff, #4169e1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            font-weight: bold;
        }
        .navbar .logo-text {
            font-size: 18px;
            font-weight: 600;
            color: white;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .navbar .nav-links {
            display: flex;
            gap: 5px;
        }
        .navbar .nav-links a { 
            display: block; 
            color: white; 
            text-align: center; 
            padding: 10px 18px; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            border-radius: 15px;
            font-weight: 500;
            font-size: 14px;
        }
        .navbar .nav-links a:hover,
        .navbar .nav-links a.active { 
            background-color: #345c6c;
            color: white; 
        }
        .navbar .right { 
            display: flex;
            align-items: center;
        }
        body {
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-image: url('assets/images/perahera2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            line-height: 1.6;
            margin: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid rgb(255, 198, 40);
        }
        h2 { text-align: center; color: #D35400; font-weight: 700; margin-bottom: 25px; letter-spacing: 1px; }
        .form-group {
            margin-bottom: 20px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: #555; text-align: left; }
        
        #seat-map-area label { text-align: center; font-size: 18px; color: #D35400; margin-bottom: 15px; }
        input[type="text"], select {
            width: 100%;
            max-width: 400px; 
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        button {
            width: 400px;
            display: block;
            margin: 20px auto 0 auto;
            background: linear-gradient(45deg, #FF9933, #D35400);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 84, 0, 0.4);
        }
       
        
        #seat-map-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .seat-example {
            width: 25px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid #777;
        }
        .seat-example.available { background-color: white; }
        .seat-example.booked { background-color: #E74C3C; } 
        .seat-example.selected { background-color: #3498DB; } 

        #seat-map-container {
            display: grid;
            grid-template-columns: repeat(11, 1fr); 
            gap: 5px;
            justify-content: center;
            padding: 15px;
            background: #f4f4f4;
            border-radius: 8px;
            border: 1px solid #ddd;
            max-width: 800px;
            margin: 0 auto;
        }
        
        
        .seat {
            width: 35px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #777;
            border-radius: 5px;
            background-color: white;
            color: black;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.2s;
            user-select: none; 
        }
        .seat:hover:not(.booked):not(.selected) {
            background-color: #f0f0f0;
            transform: scale(1.05);
        }

       
        .seat.available {
            background-color: white;
            cursor: pointer;
        }
        .seat.booked {
            background-color: #E74C3C; 
            color: white;
            cursor: not-allowed;
            border-color: #C0392B;
        }
        .seat.selected {
            background-color: #3498DB; 
            color: white;
            border: 2px solid #2980B9;
        }

        
        .seat.counter {
            background-color: #F39C12; 
            color: white;
        }
        .seat.processing {
            background-color: #2ECC71; 
            color: white;
        }

    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">LL</div>
            <div class="logo-text">PERAHERA TICKETS</div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">My Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Signup</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <h2>Book Your Perahera Tickets</h2>
         <form action="payment.php" method="POST">
            
            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="location">Select Location:</label>
                <select name="location_id" id="location" required>
                    <option value="">--- Select a Location ---</option>
                    <?php
                    $sql = "SELECT DISTINCT l.id, l.name 
                            FROM locations l
                            JOIN seats s ON l.id = s.location_id
                            WHERE s.status = 'available'";
                    $result_loc = $conn->query($sql);
                    if ($result_loc->num_rows > 0) {
                        while($row = $result_loc->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No locations with available seats</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" id="seat-map-area" style="display: none; a">
                <label>Select Your Seat:</label>
                
                <div id="seat-map-legend">
                    <div class="legend-item"><div class="seat-example available"></div> <span>Available</span></div>
                    <div class="legend-item"><div class="seat-example booked"></div> <span>Booked</span></div>
                    <div class="legend-item"><div class="seat-example selected"></div> <span>Your Selection</span></div>
                </div>

                <div id="seat-map-container">
                    </div>
                
                <input type="hidden" name="seat_id" id="selected_seat_id" value="" required>
            </div>


            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="customer_name" required>
            </div>
            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="customer_phone" required>
            </div>
            
            <button type="submit" id="submit-button" disabled>Proceed to Payment</button>
        </form>
    </div>

    <script>
        // DOM elements ටික අරගන්නවා
        const locationSelect = document.getElementById('location');
        const seatMapArea = document.getElementById('seat-map-area');
        const mapContainer = document.getElementById('seat-map-container');
        const hiddenInput = document.getElementById('selected_seat_id');
        const submitButton = document.getElementById('submit-button');

        // 1. Location එක select කරාම
        locationSelect.addEventListener('change', function() {
            const locationId = this.value;
            
            // පරණ දේවල් reset කරනවා
            mapContainer.innerHTML = '<p>Loading seats...</p>';
            hiddenInput.value = '';
            submitButton.disabled = true;

            if (locationId) {
                seatMapArea.style.display = 'block'; // Seat map එක පෙන්වනවා
                
                // 'get_seats.php' file එකෙන් data ගේනවා
                fetch('get_seats.php?location_id=' + locationId)
                    .then(response => response.json())
                    .then(seats => {
                        mapContainer.innerHTML = ''; // "Loading" අයින් කරනවා
                        
                        if (seats.length > 0) {
                            // ගෙනාපු හැම seat එකකටම div එකක් හදනවා
                            seats.forEach(seat => {
                                const seatDiv = document.createElement('div');
                                seatDiv.classList.add('seat');
                                seatDiv.classList.add(seat.status); // 'available' or 'booked' class එක
                                seatDiv.textContent = seat.seat_number;
                                
                                // Data attributes වලින් seat ID එක සහ status එක store කරනවා
                                seatDiv.dataset.seatId = seat.id;
                                seatDiv.dataset.status = seat.status;
                                
                                mapContainer.appendChild(seatDiv);
                            });
                        } else {
                            mapContainer.innerHTML = '<p>No seats found for this location.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching seats:', error);
                        mapContainer.innerHTML = '<p>Error loading seats. Please try again.</p>';
                    });
            } else {
                seatMapArea.style.display = 'none'; // Location එකක් නැත්නම් map එක හංගනවා
            }
        });

        // 2. Seat Map එකේ click කරනකොට
        mapContainer.addEventListener('click', function(e) {
            // Click කරේ seat div එකක්ද කියලා බලනවා
            if (e.target.classList.contains('seat')) {
                const clickedSeat = e.target;
                
                // Click කරේ 'available' seat එකක් නම් විතරක්
                if (clickedSeat.dataset.status === 'available') {
                    
                    // කලින් select කරපු seat එකක් තියෙනවද බලනවා
                    const previouslySelected = mapContainer.querySelector('.seat.selected');
                    
                    if (previouslySelected) {
                        // තියෙනවා නම්, ඒකේ 'selected' class එක අයින් කරනවා
                        previouslySelected.classList.remove('selected');
                    }
                    
                    // Click කරපු seat එක 'selected' කරනවා
                    clickedSeat.classList.add('selected');
                    
                    // හංගපු input එකට seat ID එක දානවා
                    hiddenInput.value = clickedSeat.dataset.seatId;
                    
                    // Submit button එක enable කරනවා
                    submitButton.disabled = false;
                }
            }
        });
    </script>
</body>
</html>