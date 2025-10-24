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
      
        .navbar { 
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-color:rgba(255, 255, 255, 0.96);
            overflow: hidden; 
            width: 100%;
            margin: 0;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-radius: 0;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            box-sizing: border-box;
            margin-bottom: 80px;
        }
        
        /* Responsive navbar */
        @media (max-width: 768px) {
            .navbar {
                padding: 10px 10px;
                flex-wrap: wrap;
            }
            .navbar .logo-text {
                font-size: 14px;
                color: #333;
            }
            .navbar .nav-links a {
                padding: 6px 6px;
                font-size: 12px;
                color: #333;
            }
            .navbar .nav-links {
                gap: 2px;
            }
        }
        
        @media (max-width: 480px) {
            .navbar {
                padding: 8px 8px;
            }
            .navbar .logo-text {
                font-size: 12px;
                color: #333;
            }
            .navbar .nav-links a {
                padding: 5px 4px;
                font-size: 11px;
                color: #333;
            }
            .navbar .logo-icon {
                width: 30px;
                height: 30px;
                font-size: 16px;
            }
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .logo-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #00bfff, #4169e1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            font-weight: bold;
        }
        .navbar .logo-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .navbar .nav-links {
            display: flex;
            gap: 17px;
        }
        .navbar .nav-links a { 
            display: block; 
            color: #333; 
            text-align: center; 
            padding: 8px 8px; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            border-radius: 6px;
            font-weight: 550;
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
            overflow-x: hidden;
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
            box-sizing: border-box;
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
            grid-template-columns: repeat(15, 1fr); 
            gap: 5px;
            justify-content: center;
            padding: 15px;
            background: #f4f4f4;
            border-radius: 8px;
            border: 1px solid #ddd;
            max-width: 90%;
            margin: 0 auto;
            max-height: 400px;
            overflow-y: auto;
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
            <div class="logo-icon1"><img src="assets/logo/logonav.png" alt="Logo" style="width: 340px; height: 50px;"></div>
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
                    $sql = "SELECT l.id, l.name, l.total_seats, l.image_url,
                                   SUM(CASE WHEN s.status = 'available' THEN 1 ELSE 0 END) as available_seats
                            FROM locations l 
                            LEFT JOIN seats s ON l.id = s.location_id 
                            GROUP BY l.id, l.name, l.total_seats, l.image_url
                            ORDER BY l.name";
                    $result_loc = $conn->query($sql);
                    if ($result_loc->num_rows > 0) {
                        while($row = $result_loc->fetch_assoc()) {
                            $available_seats = $row['available_seats'] ?? 0;
                            $total_seats = $row['total_seats'];
                            $image_url = $row['image_url'];
                            echo "<option value='" . $row['id'] . "' data-available='" . $available_seats . "' data-total='" . $total_seats . "' data-image='" . $image_url . "'>" . 
                                 $row['name'] . " (" . $available_seats . "/" . $total_seats . " seats available)</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No locations found</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" id="location-image-area" style="display: none; max-width: 100%; margin: 20px auto;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <h3 style="color: #D35400; margin-bottom: 15px; text-align: center;">Location Map</h3>
                    <div id="location-image-container" style="text-align: center;">
                        <!-- Location image will be loaded here -->
                    </div>
                </div>
            </div>

            <div class="form-group" id="seat-map-area" style="display: none; a">
                <label>Select Your Seat:</label>
                
                <div id="seat-map-legend">
                    <div class="legend-item"><div class="seat-example available"></div> <span>Available</span></div>
                    <div class="legend-item"><div class="seat-example booked"></div> <span>Booked</span></div>
                    <div class="legend-item"><div class="seat-example selected"></div> <span>Your Selection</span></div>
                </div>
                
                <div id="selected-seats-info" style="display: none; background: #e8f5e8; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #27ae60;">
                    <h4 style="margin: 0 0 10px 0; color: #27ae60;">Selected Seats:</h4>
                    <div id="selected-seats-list"></div>
                    <div id="total-price-info" style="margin-top: 10px; font-weight: bold; color: #2c3e50;"></div>
                </div>

                <div id="seat-map-container">
                    </div>
                
                <input type="hidden" name="selected_seats" id="selected_seats_data" value="" required>
            </div>


            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="customer_name" required>
            </div>
            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="customer_phone" required>
            </div>
            
            <div class="form-group" style="max-width: 400px; margin-left:auto; margin-right:auto;">
                <label for="referral_code">Referral Code (Optional):</label>
                <input type="text" id="referral_code" name="referral_code" placeholder="Enter agent code for discount">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Enter an agent code to get a discount on your ticket</small>
            </div>
            
            <button type="submit" id="submit-button" disabled>Proceed to Payment</button>
        </form>
    </div>

    <script>
        // DOM elements ටික අරගන්නවා
        const locationSelect = document.getElementById('location');
        const seatMapArea = document.getElementById('seat-map-area');
        const mapContainer = document.getElementById('seat-map-container');
        const hiddenInput = document.getElementById('selected_seats_data');
        const submitButton = document.getElementById('submit-button');
        const referralCodeInput = document.getElementById('referral_code');
        const selectedSeatsInfo = document.getElementById('selected-seats-info');
        const selectedSeatsList = document.getElementById('selected-seats-list');
        const totalPriceInfo = document.getElementById('total-price-info');
        const locationImageArea = document.getElementById('location-image-area');
        const locationImageContainer = document.getElementById('location-image-container');
        
        // Store selected seats and other data
        let selectedSeats = [];
        let currentDiscount = 0;
        let agentName = '';
        const ticketPrice = 35.00; // Base ticket price
        
        // Referral code validation
        referralCodeInput.addEventListener('input', function() {
            const referralCode = this.value.trim();
            
            if (referralCode.length > 0) {
                // Validate referral code with server
                fetch('validate_referral.php?code=' + encodeURIComponent(referralCode))
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            currentDiscount = data.discount;
                            agentName = data.agent_name;
                            this.style.borderColor = '#27ae60';
                            this.style.backgroundColor = '#d5f4e6';
                            
                            // Show discount info
                            showDiscountInfo(data.discount, data.agent_name, data.ref_count);
                            // Update price display if seats are selected
                            if (selectedSeats.length > 0) {
                                updateSelectedSeatsDisplay();
                            }
                        } else {
                            currentDiscount = 0;
                            agentName = '';
                            this.style.borderColor = '#e74c3c';
                            this.style.backgroundColor = '#fdf2f2';
                            hideDiscountInfo();
                            // Update price display if seats are selected
                            if (selectedSeats.length > 0) {
                                updateSelectedSeatsDisplay();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error validating referral code:', error);
                        currentDiscount = 0;
                        agentName = '';
                        this.style.borderColor = '#e74c3c';
                        this.style.backgroundColor = '#fdf2f2';
                        hideDiscountInfo();
                        // Update price display if seats are selected
                        if (selectedSeats.length > 0) {
                            updateSelectedSeatsDisplay();
                        }
                    });
            } else {
                currentDiscount = 0;
                agentName = '';
                this.style.borderColor = '#ddd';
                this.style.backgroundColor = 'white';
                hideDiscountInfo();
                // Update price display if seats are selected
                if (selectedSeats.length > 0) {
                    updateSelectedSeatsDisplay();
                }
            }
        });

        
        locationSelect.addEventListener('change', function() {
            const locationId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            
            // Clear previous selections
            mapContainer.innerHTML = '<p>Loading seats...</p>';
            selectedSeats = [];
            hiddenInput.value = '';
            selectedSeatsInfo.style.display = 'none';
            updateSubmitButton();

            if (locationId) {
                // Show location image
                const imageUrl = selectedOption.getAttribute('data-image');
                if (imageUrl) {
                    locationImageContainer.innerHTML = `
                        <img src="${imageUrl}" alt="Location Map" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    `;
                    locationImageArea.style.display = 'block';
                }
                
                // Show seat map
                seatMapArea.style.display = 'block';
                
                // Fetch and display seats
                fetch('get_seats.php?location_id=' + locationId)
                    .then(response => response.json())
                    .then(seats => {
                        mapContainer.innerHTML = ''; // Clear "Loading" message
                        
                        if (seats.length > 0) {
                            // Create div for each seat
                            seats.forEach(seat => {
                                const seatDiv = document.createElement('div');
                                seatDiv.classList.add('seat');
                                seatDiv.classList.add(seat.status); // 'available' or 'booked' class
                                seatDiv.textContent = seat.seat_number;
                                
                                // Store seat ID and status in data attributes
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
                // Hide location image and seat map when no location is selected
                locationImageArea.style.display = 'none';
                seatMapArea.style.display = 'none';
            }
        });

        // 2. Seat Map එකේ click කරනකොට
        mapContainer.addEventListener('click', function(e) {
            // Click කරේ seat div එකක්ද කියලා බලනවා
            if (e.target.classList.contains('seat')) {
                const clickedSeat = e.target;
                
                // Click කරේ 'available' seat එකක් නම් විතරක්
                if (clickedSeat.dataset.status === 'available') {
                    const seatId = clickedSeat.dataset.seatId;
                    const seatNumber = clickedSeat.textContent;
                    
                    // Check if seat is already selected
                    const existingIndex = selectedSeats.findIndex(seat => seat.id === seatId);
                    
                    if (existingIndex !== -1) {
                        // Remove from selection
                        selectedSeats.splice(existingIndex, 1);
                        clickedSeat.classList.remove('selected');
                    } else {
                        // Add to selection
                        selectedSeats.push({
                            id: seatId,
                            number: seatNumber,
                            location_id: locationSelect.value // Add location_id for processing
                        });
                        clickedSeat.classList.add('selected');
                    }
                    
                    // Update UI
                    updateSelectedSeatsDisplay();
                    updateSubmitButton();
                }
            }
        });
        
        // Function to update selected seats display
        function updateSelectedSeatsDisplay() {
            if (selectedSeats.length === 0) {
                selectedSeatsInfo.style.display = 'none';
                return;
            }
            
            selectedSeatsInfo.style.display = 'block';
            
            // Update seats list
            selectedSeatsList.innerHTML = selectedSeats.map(seat => 
                `<span style="background: #27ae60; color: white; padding: 5px 10px; border-radius: 15px; margin: 2px; display: inline-block;">${seat.number}</span>`
            ).join('');
            
            // Calculate total price
            const totalSeats = selectedSeats.length;
            const subtotal = totalSeats * ticketPrice;
            const discountValue = (subtotal * currentDiscount) / 100;
            const totalPrice = subtotal - discountValue;
            
            totalPriceInfo.innerHTML = `
                <div>Seats: ${totalSeats} × $${ticketPrice.toFixed(2)} = $${subtotal.toFixed(2)}</div>
                ${currentDiscount > 0 ? `<div style="color: #27ae60;">Discount (${currentDiscount}%): -$${discountValue.toFixed(2)}</div>` : ''}
                <div style="font-size: 16px; color: #2c3e50;">Total: $${totalPrice.toFixed(2)}</div>
            `;
            
            // Update hidden input with JSON data
            hiddenInput.value = JSON.stringify(selectedSeats);
        }
        
        // Function to update submit button state
        function updateSubmitButton() {
            submitButton.disabled = selectedSeats.length === 0;
            if (selectedSeats.length > 0) {
                submitButton.textContent = `Proceed to Payment (${selectedSeats.length} seat${selectedSeats.length > 1 ? 's' : ''})`;
            } else {
                submitButton.textContent = 'Proceed to Payment';
            }
        }
        
        // Functions to show/hide discount info
        function showDiscountInfo(discount, agentName, refCount) {
            // Remove existing discount info if any
            hideDiscountInfo();
            
            const discountDiv = document.createElement('div');
            discountDiv.id = 'discount-info';
            discountDiv.style.cssText = `
                background: #d5f4e6;
                border: 1px solid #27ae60;
                border-radius: 8px;
                padding: 15px;
                margin: 10px auto;
                max-width: 400px;
                text-align: center;
                color: #27ae60;
                font-weight: 600;
            `;
            discountDiv.innerHTML = `
                <div style="font-size: 18px; font-family:'Inter', sans-serif; margin-bottom: 5px;"> Discount Applied!</div>
                <div style="color: #666666;">Agent: ${agentName}</div>
                <div style="color: #666666;">Discount: ${discount}% off</div>
                <div style="font-size: 14px; margin-top: 5px; color: #666;">
                    Your ticket price will be reduced by ${discount}%
                </div>
                <div style="font-size: 12px; margin-top: 8px; color: #888; font-style: italic;">
                    Remaining uses: ${refCount}/10
                </div>
            `;
            
            referralCodeInput.parentNode.insertBefore(discountDiv, referralCodeInput.nextSibling);
        }
        
        function hideDiscountInfo() {
            const existingInfo = document.getElementById('discount-info');
            if (existingInfo) {
                existingInfo.remove();
            }
        }
        
        // Update form submission to include discount info
        document.querySelector('form').addEventListener('submit', function(e) {
            // Add hidden fields for discount info
            const discountInput = document.createElement('input');
            discountInput.type = 'hidden';
            discountInput.name = 'discount_amount';
            discountInput.value = currentDiscount;
            
            const agentInput = document.createElement('input');
            agentInput.type = 'hidden';
            agentInput.name = 'agent_name';
            agentInput.value = agentName;
            
            this.appendChild(discountInput);
            this.appendChild(agentInput);
        });
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>