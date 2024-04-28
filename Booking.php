<?php
session_start();

// ติดต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'Scinema';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $dbname);



// Check if movie_id is set in the GET request
if (isset($_GET['movie_id'])) {
    $movie_id = $_GET['movie_id'];

    // Retrieve movie details from the database
    $sql_movie_details = "SELECT * FROM Movie WHERE MovieID = $movie_id";
    $result_movie_details = $conn->query($sql_movie_details);

    // Check if the query is successful
    if ($result_movie_details && $result_movie_details->num_rows > 0) {
        // Fetch movie details
        $row_movie_details = $result_movie_details->fetch_assoc();

        // Retrieve booked seats and Showtime information from the database
        $sql_booked_seats = "SELECT SelectedSeats, Showtimetime, ShowtimeDate FROM Bookings WHERE MovieID = $movie_id";
        $result_booked_seats = $conn->query($sql_booked_seats);
        // Check if there are booked seats
        if ($result_booked_seats && $result_booked_seats->num_rows > 0) {
            // Fetch booked seats, Showtime, and ShowtimeDate
            $booked_seats = array();
            $showtimes = array();
            $showtimeDates = array();
            $web_time = isset($_GET['time']) ? $_GET['time'] : '';
            $web_date = isset($_GET['date']) ? $_GET['date'] : '';

            while ($row_booked_seats = $result_booked_seats->fetch_assoc()) {
                // Get Showtime, ShowtimeDate, and convert the format to "Y-m-d"
                $showtime = $row_booked_seats['Showtimetime'];
                $showtimeDate = date('d-m-Y', strtotime($row_booked_seats['ShowtimeDate']));

                // Check if Showtime and ShowtimeDate match the time and date from the URL
                if ($web_time == $showtime && $web_date == $showtimeDate) {
                    // Merge booked seats into the array
                    $booked_seats = array_merge($booked_seats, explode(',', $row_booked_seats['SelectedSeats']));
                    $showtimes[] = $showtime;
                    $showtimeDates[] = $showtimeDate;
                }
            }

            // Remove duplicate seat numbers
            $booked_seats = array_unique($booked_seats);

            // Display booked seats and showtimes if there are matching records
            if (!empty($booked_seats)) {
            }
        }
    } else {
        echo "ไม่พบรายละเอียดภาพยนตร์";
    }
} else {
    echo "ไม่พบรหัสภาพยนตร์";
}


$sql_room_details = "SELECT RoomID, RoomName, TypeScreen FROM Room WHERE RoomID = (SELECT RoomID FROM Movie WHERE MovieID = $movie_id)";
$result_room_details = $conn->query($sql_room_details);
if ($result_room_details->num_rows > 0) {
    $row_room_details = $result_room_details->fetch_assoc();
    $room_name = $row_room_details['RoomName'];
    $type_screen = $row_room_details['TypeScreen']; // เพิ่มการดึงข้อมูล TypeScreen
    $room_id = $row_room_details['RoomID'];
} else {
    $room_name = "ไม่พบข้อมูลห้อง";
    $type_screen = "ไม่พบข้อมูลประเภทจอ"; // กำหนดค่าว่างสำหรับ TypeScreen หากไม่พบข้อมูลห้อง
    $room_id = ""; // กำหนดค่าว่างสำหรับ RoomID หากไม่พบข้อมูลห้อง
}

$current_date = date('Y-m-d');
$sql_promotion = "SELECT DiscountAmount, PromotionName FROM Promotion WHERE StartDate <= '$current_date' AND EndDate >= '$current_date'";
$result_promotion = $conn->query($sql_promotion);

$discount_amount = 0;
$promotion_name = ""; // เพิ่มบรรทัดนี้

if ($result_promotion->num_rows > 0) {
    $row_promotion = $result_promotion->fetch_assoc();
    $discount_amount = $row_promotion['DiscountAmount'];
    $promotion_name = $row_promotion['PromotionName']; // เพิ่มบรรทัดนี้
}

// ประกาศตัวแปรสำหรับเก็บอีเมล
$email_link = '';
// เช็คว่าผู้ใช้เข้าสู่ระบบหรือยัง
if (isset($_SESSION['user_id'])) {
    // ดึง user_id จาก session
    $user_id = $_SESSION['user_id'];

    // ดึงอีเมลของผู้ใช้ที่ล็อกอินอยู่
    $sql_email = "SELECT Email FROM Users WHERE UsersID = $user_id";
    $result_email = $conn->query($sql_email);

    if ($result_email->num_rows > 0) {
        $row_email = $result_email->fetch_assoc();
        // กำหนดลิงก์อีเมลล์
        $email_link = '<a href="#" class="email-link">Email: ' . $row_email['Email'] . '</a>';
    }
}

// ตรวจสอบ Session ที่ตั้งค่าไว้
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงคะแนนสะสมจากฐานข้อมูล
    $sql_user_points = "SELECT Point FROM Users WHERE UsersID = $user_id";
    $result_user_points = $conn->query($sql_user_points);

    if ($result_user_points && $result_user_points->num_rows > 0) {
        $row_user_points = $result_user_points->fetch_assoc();
        $accumulated_points = $row_user_points['Point'];
    } else {
        echo "<p>ไม่สามารถดึงข้อมูลคะแนนสะสมได้</p>";
    }

    // ดึง Point ที่เก็บไว้ในตัวแปร $points จากภาคต่างๆ ของโค้ด
    $existing_points = isset($points) ? $points : 0;
} else {
    echo "<p>Session user_id ไม่ได้ถูกตั้งค่าหรือไม่มี</p>";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS Styles */
        body {
            margin: 0;
            background-color: #000;
            color: #FFF;
            font-family: Arial, sans-serif;
            margin-top: 70px;
            /* เพิ่ม margin-top เพื่อห่างจากบาร์ด้านบน */
        }

        nav {
            background-color: #212121;
            padding-top: 25px;
            padding-bottom: 25px;
            text-align: center;
            top: 0;
            width: 100%;
            z-index: 1000;
            position: fixed;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            position: relative;
            transition: color 0.3s ease-in-out;
        }

        .nav-link:hover {
            color: #FF3187;
        }

        .nav-link:hover .user-icon {
            bottom: 10px;
        }

        .nav-link:hover i {
            transform: translateY(-5px);
        }

        i {
            transition: transform 0.3s ease-in-out;
        }

        section {
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 800px;
            /* Limiting the width of the content */
        }

        footer {
            background-color: #212121;
            padding: 10px;
            text-align: center;
            width: 100%;
        }

        .movie-details {
            display: flex;
            justify-content: space-between;
            /* Aligning the items with space between */
            align-items: center;
            /* Vertically centering the items */
        }

        .movie-poster {
            width: 300px;
            margin-right: 20px;
        }

        .movie-details-text {
            flex: 1;
            text-align: left;
        }

        .seat-btn {
            width: 40px;
            height: 40px;
            margin: 5px;
            background-color: #FF3187;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .movie-details {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .movie-details-text {
            max-width: 400px;
            /* ปรับขนาดตามที่ต้องการ */
            margin: 0 20px;
            /* ปรับขนาดตามที่ต้องการ */
        }

        #seatContainer {
            display: flex;
            justify-content: center;
            /* ตำแหน่งที่นั่งกลางตามแนวนอน */
            align-items: center;
            /* ตำแหน่งที่นั่งกลางตามแนวตั้ง */
            flex-wrap: wrap;
            max-width: 600px;
            /* ปรับขนาดตามที่ต้องการ */
            margin: 20px 0;
            /* ปรับขนาดตามที่ต้องการ */
        }

        #seatContainer div {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: auto;
            /* ที่นั่งไปชิดขวาสุด */
        }

        .seat-btn {
            margin: 5px;
        }

        .seat-btn.selected {
            background-color: #8D003B;
            /* Changing the color when selected */
        }

        .seat-btn:hover {
            background-color: #722B48;
        }

        /* เพิ่มสีสำหรับที่นั่งที่ถูกจองแล้ว */
        .seat-btn.booked {
            background-color: #E9953C;
            pointer-events: none;
            /* ทำให้ไม่สามารถคลิกได้ */
            cursor: not-allowed;
            /* เปลี่ยน cursor เป็น not-allowed */
        }

        /* เพิ่มสีสำหรับที่นั่งที่ถูกเลือก */
        .seat-btn.selected {
            background-color: #8D003B;
        }

        h1 {
            margin-top: 20px;
            /* Adding top margin to h1 */
        }

        h2 {
            text-align: center;
            /* Centering the h2 */
        }

        .user-icon {
            right: 10px;
            width: 50px;
            height: 50px;
            background: url('https://www.majorcineplex.com/public/mobile_new_assets/img/avatar.png') no-repeat center center;
            background-size: cover;
            border-radius: 50%;
            cursor: pointer;
            transition: bottom 0.5s ease-in-out;
            position: absolute;
            /* Position the user icon absolutely within the .nav-link */
            bottom: 10px;
            /* Initially set to 10px from the bottom */
        }

        /* สไตล์ CSS สำหรับแถบข้อความที่ยื่นออกมา */
        .menu-container {
            display: none;
            position: fixed;
            top: 70px;
            right: 10px;
            background-color: #333;
            color: #FFF;
            padding: 10px;
            border-radius: 5px;
            flex-direction: column;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .menu-item {
            text-align: left;
            padding: 10px;
            /* เพิ่ม Padding เพื่อให้มีพื้นที่รอบๆ ข้อความ */
            transition: background-color 0.3s ease;
            /* เพิ่มเอฟเฟกต์ transition สำหรับเปลี่ยนสีพื้นหลัง */
        }

        .menu-item:hover {
            background-color: #555;
            /* เปลี่ยนสีพื้นหลังเมื่อนำเมาส์ hover */
        }

        .menu-item::before {
            content: '\2022';
            /* รหัส Unicode สำหรับเครื่องหมายดอกจัน (•) */
            color: #FF3187;
            /* สีไอคอน */
            margin-right: 5px;
            /* ระยะห่างระหว่างไอคอนกับข้อความ */
        }

        body {
            margin-top: 70px;
        }

        .menu-container {
            text-align: left;
        }

        .email-container {
            position: fixed;
            top: 15px;
            right: 60px;
            padding: 10px;
            z-index: 1001;
        }

        .email-link {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #333;
            /* เปลี่ยนสีพื้นหลังเป็นสีเทาเข้ม */
            margin-top: 0px;
            /* ปรับตำแหน่งของ email-link ขึ้น */
        }

        .email-link:hover {
            background-color: #444;
            /* เปลี่ยนสีพื้นหลังเมื่อโฮเวอร์เป็นสีเทาเข้มขึ้น */
        }

        .button {
            text-align: center;
            margin-top: 20px;
        }

        .button button {
            background-color: #670015;
            /* เปลี่ยนสีพื้นหลังปุ่ม */
            color: white;
            padding: 4px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            /* เพิ่มการเปลี่ยนสีพื้นหลังอย่างน้อย 0.3 วินาที */
        }

        .button button:hover {
            background-color: #ff6699;
            /* เปลี่ยนสีพื้นหลังปุ่มเมื่อเม้าส์ชี้ */
        }
    </style>
</head>

<body>

    <nav>
        <div class="user-icon" id="userIcon" onclick="toggleMenu()"></div>
        <div class="menu-container" id="menuContainer">
            <div class="menu-item" id="editInfo">Edit personal</div>
            <div class="menu-item" id="userPoint">Point : <?php echo ($accumulated_points + $existing_points) ?></div>
            <div class="menu-item" id="logOut">Log out</div>
        </div>
        <a href="headder.php" class="nav-link"><i class="fas fa-home"></i> Homepage</a>
        <a href="homepage.php" class="nav-link"><i class="fas fa-film"></i> Movie</a>
        <a href="promotion.php" class="nav-link"><i class="fas fa-percent"></i> Promotion</a>
    </nav>

    <div class="email-container">
        <?php echo $email_link; ?>
    </div>


    <h1>Booking movie tickets</h1>
    <section>
        <!-- รายละเอียดการจอง -->
        <div class="booking-details">
        </div>

        <!-- รายละเอียดของภาพยนตร์ -->
        <div class="movie-details">
            <div class="movie-poster">
                <img src="<?php echo $row_movie_details['Poster']; ?>" alt="<?php echo $row_movie_details['NameMovie']; ?>" style="width: 100%;">
            </div>
            <div class="movie-details-text">
                <h3><?php echo $row_movie_details['NameMovie']; ?></h3>
                <p>Selected date : <?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?></p>
                <p>Movie price : <?php echo $row_movie_details['price'] ?> Bath</p>
                <p>Showtime : <?php echo isset($_GET['time']) ? $_GET['time'] : ''; ?></p>
                <p>Runtime : <?php echo $row_movie_details['Duration']; ?></p>
                <p>Room : <?php echo $room_name; ?></p>
                <p>Screen type : <?php echo $type_screen; ?></p> <!-- แสดงประเภทจอ -->
                <?php if (!empty($promotion_name)) : ?>
                    <p>Promotion : <?php echo $promotion_name; ?></p>
                <?php endif; ?>
                <p>Price to pay : <span id="totalPrice">
                        <p>Point : <?php echo isset($accumulated_points) ? $accumulated_points : 'N/A'; ?> Point</p>
                        <!-- เพิ่มส่วนใน form สำหรับเลือกใช้ Point -->
                        <label for="usePoints">Click to use points :</label>
                        <input type="checkbox" id="usePoints" name="paymentMethod" value="points" onchange="togglePointsInput()">
                        <input type="number" id="pointsInput" name="pointsInput" placeholder="Number of points" readonly>
                        <p>Selected seat : <span id="selectedSeats">-</span></p>
                        <p>Number of seats selected : <span id="selectedSeatsCountDisplay">0 </span> Seat</p>
                        <label>Select the desired seat</label>
            </div>
        </div>

        <form id="bookingForm" method="post" action="submit.php">
            <!-- Displaying seat buttons -->
            <div id="seatContainer">
                <?php
                $seats = array('A', 'B', 'C', 'D', 'E');
                foreach ($seats as $row) {
                    echo "<div>";
                    for ($i = 1; $i <= 8; $i++) {
                        $seatNumber = $row . str_pad($i, 2, '0', STR_PAD_LEFT);

                        // Check if the seat is booked
                        $isBooked = in_array($seatNumber, $booked_seats);

                        // Add class 'booked' if the seat is booked
                        $seatClass = $isBooked ? 'booked' : '';

                        echo "<button class='seat-btn $seatClass' type='button' data-seat='$seatNumber'>$seatNumber</button>";
                    }
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Hidden inputs to store selected seats and room name -->
            <input type="hidden" name="pointsUsed" id="pointsUsedInput">
            <input type="hidden" id="selectedSeatsInput" name="selected_seats" value="">
            <input type="hidden" name="room_name" value="<?php echo $room_name; ?>">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <input type="hidden" name="totalPrice" id="totalPriceInput">
            <input type="hidden" name="selected_seats_count" id="selectedSeatsCount" name="selected_seats_count" value="0">

            <div class="button">
                <button id="button" type="button">Confirm booking</button>
            </div>
        </form>
    </section>

    <script>
        // เรียกใช้งาน DOMContentLoaded เพื่อรอให้หน้าเว็บโหลดเสร็จสมบูรณ์ก่อน
        document.addEventListener('DOMContentLoaded', function() {
            // ปิดเมนูเมื่อหน้าเว็บโหลด
            menuContainer.style.display = 'none';

            // เพิ่ม event listener เมื่อคลิกที่ไอคอนผู้ใช้
            userIcon.addEventListener('click', function() {
                // สลับการแสดงผลของเมนู
                menuContainer.style.display = (menuContainer.style.display === 'none' || menuContainer.style.display === '') ? 'block' : 'none';
            });

            // Redirect to logout page on logout button click
            document.getElementById("logOut").addEventListener("click", function() {
                window.location.href = "logout.php";
            });
            document.getElementById("editInfo").addEventListener("click", function() {
                // ตรวจสอบว่าตัวแปร $ruser_id['Users'] มีค่าหรือไม่
                var user_id = "<?php echo $user_id['UsersID']; ?>";
                window.location.href = "edit_profile.php?user_id=" + user_id;
            });
        });
    </script>

    <script>
        document.getElementById('button').addEventListener('click', function() {
            const selectedSeats = document.querySelectorAll('.seat-btn.selected');

            // ตรวจสอบว่ามีที่นั่งที่ถูกเลือกอย่างน้อย 1 ที่นั่ง
            if (selectedSeats.length === 0) {
                alert('กรุณาเลือกที่นั่งก่อนทำการยืนยันการจอง');
                return; // ไม่ดำเนินการต่อกับการส่งฟอร์ม
            // เมื่อกดปุ่ม "ตกลง" ให้รีเฟรชหน้าเว็บ
            window.location.reload();
            }
            const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.dataset.seat);
            const selectedSeatsInput = document.getElementById('selectedSeatsInput');
            selectedSeatsInput.value = selectedSeatNumbers.join(',');

            // เพิ่มข้อมูลใน URL parameters และทำการ submit ฟอร์ม
            const bookingForm = document.getElementById('bookingForm');
            const movieNameInput = document.createElement('input');
            movieNameInput.type = 'hidden';
            movieNameInput.name = 'movie_name';
            movieNameInput.value = "<?php echo $row_movie_details['NameMovie']; ?>";
            bookingForm.appendChild(movieNameInput);

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'date';
            dateInput.value = "<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>";
            bookingForm.appendChild(dateInput);

            const timeInput = document.createElement('input');
            timeInput.type = 'hidden';
            timeInput.name = 'time';
            timeInput.value = "<?php echo isset($_GET['time']) ? $_GET['time'] : ''; ?>";
            bookingForm.appendChild(timeInput);

            const totalPriceInput = document.createElement('input');
            totalPriceInput.type = 'hidden';
            totalPriceInput.name = 'totalPrice';
            const pointsUsed = parseInt(document.getElementById('pointsInput').value) || 0; // นิยาม pointsUsed ที่นี่
            let totalPrice = selectedSeats.length * (<?php echo $row_movie_details['price'] - $discount_amount; ?>);
            totalPrice -= pointsUsed;
            totalPriceInput.value = totalPrice > 0 ? totalPrice : 0;
            bookingForm.appendChild(totalPriceInput);

            bookingForm.submit();
        });

        const seatButtons = document.querySelectorAll('.seat-btn');
        seatButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (!button.classList.contains('booked')) {
                    if (!button.classList.contains('selected')) {
                        button.classList.add('selected');
                    } else {
                        button.classList.remove('selected');
                    }

                    updateSelectedSeats();
                    updateSelectedSeatsCount();
                    updateTotalPrice();
                    updateHiddenInputs(); // Update hidden inputs on seat click
                }
            });
        });

        function updateHiddenInputs() {
            const selectedSeats = document.querySelectorAll('.seat-btn.selected');
            const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.dataset.seat);

            // Update the hidden input fields with current values
            document.getElementById('selectedSeatsInput').value = selectedSeatNumbers.join(',');
            document.getElementById('totalPriceInput').value = selectedSeats.length * <?php echo $row_movie_details['price'] - $discount_amount; ?>;
        }

        function updateSelectedSeats() {
            const selectedSeats = document.querySelectorAll('.seat-btn.selected');
            const selectedSeatNumbers = Array.from(selectedSeats).map(seat => seat.dataset.seat);
            const totalPriceSpan = document.getElementById('totalPrice');
            const selectedSeatsSpan = document.getElementById('selectedSeats');
            const totalPrice = selectedSeats.length * <?php echo $row_movie_details['price'] - $discount_amount; ?>;

            document.getElementById('selectedSeatsInput').value = selectedSeatNumbers.join(',');
            totalPriceSpan.textContent = totalPrice > 0 ? totalPrice + ' Bath' : '-';
            selectedSeatsSpan.textContent = selectedSeatNumbers.join(', ');
        }

        function updateSelectedSeatsCount() {
            const selectedSeats = document.querySelectorAll('.seat-btn.selected');
            const selectedSeatsCountInput = document.getElementById('selectedSeatsCount');
            selectedSeatsCountInput.value = selectedSeats.length;

            // อัพเดทจำนวนที่นั่งที่ถูกเลือกที่แสดงบนเว็บ
            const selectedSeatsCountDisplay = document.getElementById('selectedSeatsCountDisplay');
            selectedSeatsCountDisplay.textContent = selectedSeats.length;
        }

        function updateTotalPrice() {
            const selectedSeats = document.querySelectorAll('.seat-btn.selected');
            const pricePerSeat = <?php echo $row_movie_details['price']; ?>;
            const discountAmount = <?php echo $discount_amount; ?>;
            const numSeats = selectedSeats.length;

            let totalPriceBeforeDiscount = pricePerSeat * numSeats;
            let totalPriceAfterDiscount = totalPriceBeforeDiscount - (discountAmount * numSeats);

            // ถ้า checkbox ถูกเลือก
            if (document.getElementById('usePoints').checked) {
                const pointsUsed = parseInt(document.getElementById('pointsInput').value) || 0;
                totalPriceAfterDiscount -= pointsUsed;
            }

            // ไม่ให้ราคาติดลบ
            totalPriceAfterDiscount = Math.max(totalPriceAfterDiscount, 0);

            console.log('totalPriceAfterDiscount:', totalPriceAfterDiscount); // เพิ่มบรรทัดนี้
            document.getElementById('totalPrice').textContent = `${totalPriceAfterDiscount} Bath`;
            document.getElementById('totalPriceInput').value = totalPriceAfterDiscount;
            console.log(totalPriceAfterDiscount); // แสดงค่า totalPriceAfterDiscount ใน console
            console.log(document.getElementById('totalPrice').textContent); // แสดงค่าใน element totalPrice ใน console
            console.log(document.getElementById('totalPriceInput').value); // แสดงค่าใน input totalPrice ใน console

        }

        document.addEventListener('DOMContentLoaded', function() {
            togglePointsInput(); // เรียกฟังก์ชันเพื่อตั้งค่าตามสถานะเริ่มต้น
            updatePointsInputMinMax(); // เรียกฟังก์ชันเพื่อตั้งค่า min และ max
            adjustInputSizeToPlaceholder(); // เรียกฟังก์ชันเพื่อปรับขนาด input
            // เมื่อ checkbox ถูกเปลี่ยนแปลง
            document.getElementById('usePoints').addEventListener('change', function() {
                updateTotalPrice(); // เรียกใช้ฟังก์ชันเพื่ออัปเดตราคาที่ต้องจ่าย
            });
        });

        document.getElementById('usePoints').addEventListener('change', function() {
            togglePointsInput(); // เรียกฟังก์ชันเพื่อสลับการแสดง/ซ่อนช่อง input
            updatePointsInputMinMax(); // เรียกฟังก์ชันเพื่อตั้งค่า min และ max
            adjustInputSizeToPlaceholder(); // เรียกฟังก์ชันเพื่อปรับขนาด input
            // เมื่อมีการเปลี่ยนแปลงใน input pointsInput
            document.getElementById('pointsInput').addEventListener('input', function() {
                updateTotalPrice(); // เรียกใช้ฟังก์ชันเพื่ออัปเดตราคาที่ต้องจ่าย
            });
        });

        function adjustInputSizeToPlaceholder() {
            var pointsInput = document.getElementById('pointsInput');
            var placeholderText = pointsInput.placeholder;

            // ตั้งค่า style ของ input ให้มีความกว้างเท่ากับขนาดของ placeholder
            pointsInput.style.width = (placeholderText.length * 9) + 'px';
        }


        function updatePointsInputMinMax() {
            var pointsInput = document.getElementById('pointsInput');
            pointsInput.min = 1; // ตั้งค่า min เป็น 1
            pointsInput.max = <?php echo $accumulated_points; ?>; // ตั้งค่า max เป็นจำนวน Point ที่ผู้ใช้มี
        }

        function togglePointsInput() {
            var usePointsCheckbox = document.getElementById('usePoints');
            var pointsInput = document.getElementById('pointsInput');
            var accumulatedPoints = <?php echo $accumulated_points; ?>;

            if (usePointsCheckbox.checked) {
                pointsInput.removeAttribute('readonly');
                pointsInput.style.display = 'inline';
                pointsInput.max = accumulatedPoints; // ตั้งค่า max เท่ากับจำนวน point ที่มีอยู่
            } else {
                pointsInput.setAttribute('readonly', 'readonly');
                pointsInput.style.display = 'none';
            }

            updateTotalPrice();
        }

        document.getElementById('button').addEventListener('click', function() {

            const pointsUsed = parseInt(document.getElementById('pointsInput').value) || 0;
            const pointsUsedInput = document.getElementById('pointsUsedInput');
            pointsUsedInput.value = pointsUsed;

            const bookingForm = document.getElementById('bookingForm');
            bookingForm.submit();
        });


        // ในส่วน script ของ HTML
        document.getElementById('pointsInput').addEventListener('input', function() {
            var pointsInput = this;
            var enteredPoints = parseInt(pointsInput.value);

            if (enteredPoints < 1 || enteredPoints > <?php echo $accumulated_points; ?>) {
                console.log('กรุณากรอกจำนวนแต้มที่ถูกต้อง');
                pointsInput.value = ''; // ล้างค่าที่กรอกเพื่อให้ผู้ใช้กรอกใหม่
            }
        });



        document.getElementById('selectedSeatsInput').addEventListener('change', updateTotalPrice);

        // เรียกใช้ฟังก์ชันเพื่อเริ่มต้น
        updateSelectedSeats();
        updateSelectedSeatsCount();
        updateTotalPrice();
    </script>


</body>

</html>