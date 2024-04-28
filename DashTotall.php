<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ข้อมูลยอดขาย</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* สไตล์ CSS ที่ใช้ในหน้า moviemore.php */
        body {
            background-color: ##f2f2ed;
            color: #212121;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        .logout-btn {
            color: #FFF;
            background-color: rgba(255, 99, 132, 1);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            float: right;
            /* จัดให้ปุ่มอยู่ทางขวาสุด */
            margin-top: -43px;
            /* ให้มีระยะห่างด้านบน */
        }

        .logout-btn:hover {
            background-color: #9E1D5D;
        }

        nav {
            background-color: rgba(255, 99, 132, 1);
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #FFF;
            text-decoration: none;
            padding: 10px;
            margin: 0 10px;
        }

        section {
            padding: 20px;
            text-align: center;
        }

        footer {
            background-color: #212121;
            padding: 10px;
            text-align: center;
        }

        .movie-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-poster {
            width: 300px;
            margin-right: 20px;
        }

        .movie-details-text {
            flex: 1;
            text-align: left;
        }

        .btn-book {
            display: inline-block;
            padding: 10px 30px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 250px;
        }

        /* เพิ่มสไตล์ปุ่ม */
        .btn-more {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-book:hover {
            background-color: #2980b9;
        }

        /* เพิ่มสไตล์ Canvas */
        canvas {
            max-width: 800px;
            /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin: 0 auto;
        }

        /* เพิ่มสไตล์กราฟแท่งและกราฟวงกลม */
        #salesChartsContainer {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
            /* ระยะห่างข้างล่างของ Container */
        }

        /* เพิ่มสไตล์กราฟแท่ง */
        #genreSalesChart {
            height: 400px;
            max-width: 5000px;
            /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto;
            /* จัดกราฟไปที่กลาง */
            margin-top: -150px;
        }

        /* เพิ่มสไตล์กราฟวงกลม */
        #movieSalesChart {
            max-width: 600px;
            /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto;
            /* จัดกราฟไปที่กลาง */
            margin-top: -150px;
            /* ปรับตำแหน่งตามความต้องการ */
        }

        /* เพิ่มสไตล์กราฟแท่งตามเพศ */
        #genderSalesChart {
            max-width: 600px;
            /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto;
            /* จัดกราฟไปที่กลาง */
        }

        /* เพิ่มสไตล์กราฟแท่งจำนวนผู้ใช้ */
        #genderChart {
            max-width: 600px;
            /* ปรับขนาดของกราฟตามความเหมาะสม */
            margin-left: 20px auto;
            /* จัดกราฟไปที่กลาง */
        }

        .sales-summary {
            text-align: center;
            margin-right: 30px;
        }

        .sales-item {
            display: inline-block;
            margin: 0 10px;
            text-align: left;
        }
    </style>
</head>
<nav>
    <a href="DashTotall.php?user_id=<?php echo $user_id; ?>">หน้าหลัก</a>
    <a href="Dash.php">ยอดขายเจาะจง</a>

</nav>
<a href="logoutceo.php" class="logout-btn">Logout</a>

<body>
    <div class="container">
        <h1>Dashboard - ข้อมูลยอดขาย</h1>

        <div class="sales-summary">
            <h2>ยอดขายต่อ ประเภท ของหนัง</h2>
            <div class="sales-item">
                <span>ยอดขายรวม:</span>
                <span class="total-sales">
                    <?php
                    // เชื่อมต่อกับฐานข้อมูล
                    $host = 'localhost';
                    $dbname = 'Scinema';
                    $username = 'root';
                    $password = 'root';

                    $conn = new mysqli($host, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
                    }

                    // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายรวม
                    $sql_total_sales = "SELECT SUM(TotalPrice) AS total_sales FROM bookings";
                    $result_total_sales = $conn->query($sql_total_sales);
                    $row_total_sales = $result_total_sales->fetch_assoc();
                    echo $row_total_sales['total_sales'];

                    ?> Bath
                </span>
            </div>
        </div>

        <!-- สร้าง canvas element สำหรับแสดงกราฟแท่ง -->
        <!-- สร้าง canvas element สำหรับแสดงกราฟแท่ง -->
        <canvas id="genreSalesChart" width="400" height="200" style="margin-top: 50px;"></canvas>


        <script>
            <?php
            // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละประเภทหนัง
            $sql_genre_sales = "SELECT g.Genre, SUM(b.TotalPrice) AS total_sales
                                    FROM bookings b
                                    INNER JOIN movie m ON b.MovieID = m.MovieID
                                    INNER JOIN genre g ON m.GenreID = g.GenreID
                                    GROUP BY g.Genre";

            $result_genre_sales = $conn->query($sql_genre_sales);

            $genres = array();
            $genreSalesData = array();

            if ($result_genre_sales->num_rows > 0) {
                while ($row = $result_genre_sales->fetch_assoc()) {
                    $genres[] = $row["Genre"];
                    $genreSalesData[] = $row["total_sales"];
                }
            }
            ?>

            //สร้างกราฟแท่ง
            var ctxGenre = document.getElementById('genreSalesChart').getContext('2d');
            var genreSalesChart = new Chart(ctxGenre, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($genres); ?>,
                    datasets: [{
                        label: 'ยอดขายตามประเภทหนัง (บาท)',
                        data: <?php echo json_encode($genreSalesData); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

        <div class="sales-summary">
            <h2>ยอดขายต่อ เรื่อง</h2>
        </div>
        <canvas id="movieSalesChart" width="600" height="200" style="margin-top: 50px;"></canvas>
        <script>
            // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละหนัง
            <?php
            $sql_movie_sales = "SELECT Namemovie, SUM(TotalPrice) AS total_sales
                    FROM bookings
                    GROUP BY Namemovie";
            $result_movie_sales = $conn->query($sql_movie_sales);

            $movieNames = array();
            $movieSalesData = array();

            if ($result_movie_sales->num_rows > 0) {
                while ($row = $result_movie_sales->fetch_assoc()) {
                    $movieNames[] = $row["Namemovie"];
                    $movieSalesData[] = $row["total_sales"];
                }
            }
            ?>

            // สร้างกราฟวงกลม
            var ctxMovie = document.getElementById('movieSalesChart').getContext('2d');
            var movieSalesChart = new Chart(ctxMovie, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($movieNames); ?>,
                    datasets: [{
                        label: 'ยอดขาย (บาท)',
                        data: <?php echo json_encode($movieSalesData); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

        <div class="sales-summary">
            <table class="sales-item">
                <thead>
                    <tr>
                        <th>หนัง</th>
                        <th>ยอดขาย (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // คำสั่ง SQL เพื่อดึงข้อมูลยอดขายแต่ละรายการ
                    $sql_individual_sales = "SELECT Namemovie, SUM(TotalPrice) AS total_sales
                                            FROM bookings
                                            GROUP BY Namemovie
                                            ORDER BY total_sales DESC";

                    $result_individual_sales = $conn->query($sql_individual_sales);

                    if ($result_individual_sales->num_rows > 0) {
                        // แสดงรายการยอดขายแต่ละรายการ
                        while ($row = $result_individual_sales->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Namemovie"] . "</td>";
                            echo "<td>" . $row["total_sales"] . " Bath</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>ไม่พบข้อมูลยอดขายแบบเจาะจง</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="sales-summary" id="genreSalesSection">
            <h2>จำนวนของผู้ใช้ทั้งหมด (เพศ)</h2>
        </div>
        <canvas id="genderChart" width="500" height="200"></canvas>
        <script>
            <?php
            // โค้ด SQL สำหรับดึงข้อมูลยอดขายตามเพศ
            $sql_gender_sales = "SELECT u.Gender, SUM(b.TotalPrice) AS total_sales
                            FROM bookings b
                            INNER JOIN users u ON b.UsersID = u.UsersID
                            WHERE b.BookingDate BETWEEN '$start_date' AND '$end_date'
                            GROUP BY u.Gender
                            ORDER BY total_sales";

            $result_gender_sales = $conn->query($sql_gender_sales);

            $genders = array();
            $genderSalesData = array();

            if ($result_gender_sales->num_rows > 0) {
                while ($row = $result_gender_sales->fetch_assoc()) {
                    $genders[] = $row["Gender"];
                    $genderSalesData[] = $row["total_sales"];
                }
            }
            ?>

            <?php
            $sql_gender_sales = "SELECT u.Gender, SUM(b.TotalPrice) AS total_sales
                            FROM bookings b
                            INNER JOIN users u ON b.UsersID = u.UsersID
                            GROUP BY u.Gender
                            ORDER BY total_sales";

            $result_gender_sales = $conn->query($sql_gender_sales);

            $genders = array();
            $genderSalesData = array();

            if ($result_gender_sales->num_rows > 0) {
                while ($row = $result_gender_sales->fetch_assoc()) {
                    $genders[] = $row["Gender"];
                    $genderSalesData[] = $row["total_sales"];
                }
            }
            ?>

                <
                !--โค้ ดสร้ างกราฟแท่ งตามเพศ-- >
                <
                script >
                var ctxGender = document.getElementById('genderSalesChart').getContext('2d');
            var genderSalesChart = new Chart(ctxGender, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($genders); ?>,
                    datasets: [{
                        label: 'ยอดขายตามเพศ (บาท)',
                        data: <?php echo json_encode($genderSalesData); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>


        <div class="sales-summary" id="genreSalesSection">
            <h2>ยอดขายต่อ เพศ ของผู้ใช้</h2>
        </div>
        <canvas id="genderSalesChart" width="500" height="200"></canvas>
        <!-- โค้ดสร้างกราฟแท่งตามเพศ -->
        <script>
            var ctxGender = document.getElementById('genderSalesChart').getContext('2d');
            var genderSalesChart = new Chart(ctxGender, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($genders); ?>,
                    datasets: [{
                        label: 'ยอดขายตามเพศ (บาท)',
                        data: <?php echo json_encode($genderSalesData); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>

        <canvas id="genderChart" width="400" height="200"></canvas>
        <script>
            <?php
            // คิวรี่ข้อมูล gender จากตาราง users
            $sql = "SELECT gender, COUNT(*) AS count FROM users GROUP BY gender";
            $result = $conn->query($sql);

            // สร้างข้อมูลสำหรับกราฟ
            $labels = array();
            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $labels[] = $row['gender'];
                    $data[] = $row['count'];
                }
            }
            ?>

            // สร้างกราฟแท่ง
            var ctx = document.getElementById('genderChart').getContext('2d');
            var genderChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{

                        label: 'จำนวนผู้ใช้',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                stepSize: 1,
                                beginAtZero: true,
                                // ไม่ต้องระบุ max หรือปล่อยให้กราฟปรับตามข้อมูล
                            }
                        }]
                    }
                }
            });
        </script>
        <?php
        $sql_check_date_type = "SELECT COLUMN_TYPE 
                                    FROM INFORMATION_SCHEMA.COLUMNS 
                                    WHERE TABLE_NAME = 'bookings' 
                                    AND COLUMN_NAME = 'BookingDate'";

        $result_check_date_type = $conn->query($sql_check_date_type);

        if ($result_check_date_type->num_rows > 0) {
            $row_check_date_type = $result_check_date_type->fetch_assoc();
            if (strpos($row_check_date_type['COLUMN_TYPE'], 'date') === false) {
                // แปลงฟิลด์ BookingDate เป็นวันที่
                $sql_convert_to_date = "ALTER TABLE bookings MODIFY BookingDate DATE";
                $conn->query($sql_convert_to_date);
            }
        }

        $sql_monthly_sales = "SELECT DATE_FORMAT(BookingDate, '%M %Y') AS month, SUM(TotalPrice) AS total_sales
                      FROM bookings
                      GROUP BY month";

        $result_monthly_sales = $conn->query($sql_monthly_sales);

        $months = array();
        $monthlySalesData = array();

        if ($result_monthly_sales->num_rows > 0) {
            // ดึงข้อมูลทั้งหมดเพื่อนำไปเรียงลำดับตามวันที่
            $allMonthlySalesData = array();
            $currentYearData = array();

            while ($row = $result_monthly_sales->fetch_assoc()) {
                // เปลี่ยนเดือนเป็นเลขที่แทนเดือน
                $monthNumber = date_parse($row["month"])["month"];
                $year = date_parse($row["month"])["year"];

                if ($year === date('Y')) {
                    // ถ้าเป็นปีปัจจุบันให้เก็บไว้ใน array ปัจจุบัน
                    $currentYearData[$monthNumber] = $row["total_sales"];
                } else {
                    // ถ้าไม่ใช่ปีปัจจุบันให้เก็บไว้ใน array ทั้งหมด
                    $allMonthlySalesData[$year][$monthNumber] = $row["total_sales"];
                }
            }

            // เรียงลำดับข้อมูลตามปีและเดือน และเก็บปีปัจจุบันไว้ข้างหลัง
            krsort($allMonthlySalesData);
            ksort($currentYearData);

            $months = array();
            $monthlySalesData = array();

            foreach ($allMonthlySalesData as $year => $monthlySales) {
                foreach ($monthlySales as $monthNumber => $totalSales) {
                    $months[] = date("F Y", mktime(0, 0, 0, $monthNumber, 1, $year));
                    $monthlySalesData[] = $totalSales;
                }
            }

            // เพิ่มข้อมูลปีปัจจุบันไว้ท้ายสุด
            foreach ($currentYearData as $monthNumber => $totalSales) {
                $months[] = date("F Y", mktime(0, 0, 0, $monthNumber, 1));
                $monthlySalesData[] = $totalSales;
            }
        }

        ?>
        <div class="sales-summary">
            <h2>ยอดขายตามเดือน</h2>
        </div>
        <div class="monthly-sales-chart-container">
            <canvas id="monthlySalesChart" width="500" height="400"></canvas>
        </div>
        <script>
            var ctxMonthlySales = document.getElementById('monthlySalesChart').getContext('2d');
            var monthlySalesChart = new Chart(ctxMonthlySales, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($months); ?>,
                    datasets: [{
                        label: 'ยอดขายตามเดือน (บาท)',
                        data: <?php echo json_encode($monthlySalesData); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            categoryPercentage: 0.7 // ปรับกว้างของแท่ง
                        }]
                    }
                }
            });
        </script>
    </div>
</body>

</html>