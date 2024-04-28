
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S Cinema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #555;
            padding: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }

        .container {
            padding: 20px;
        }

        section {
            margin: 20px 0;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 5px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .image-container {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            white-space: nowrap;
        }

        .image-container img {
            width: 100%;
            max-width: 10000px;
            margin-right: 10px;
            scroll-snap-align: start;
        }

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center; /* เพิ่มบรรทัดนี้เพื่อให้ภาพอยู่ตรงกลาง */
        }

        .movie {
            width: 300px; /* กำหนดความกว้างของ .movie โดยไม่ให้ขยายเท่ากับขนาดของภาพ */
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            margin: 15 15px; /* เพิ่ม margin เพื่อลดระยะห่างระหว่าง .movie */
        }

        .movie img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .movie img {
            width: 300px; /* กำหนดความกว้างเป็นค่าคงที่ที่คุณต้องการ */
            height: 350px; /* กำหนดความสูงเป็นค่าคงที่ที่คุณต้องการ */
            object-fit: cover; /* ให้รูปทำการ cover พื้นที่ที่กำหนดไว้ */
            border-radius: 8px;
        }
        /* เพิ่มสไตล์ CSS เพื่อทำให้ตัวหนังสือเป็นสีดำและไม่มีเส้นใต้ล่าง */
        .movie a {
            text-decoration: none; /* ลบเส้นใต้ล่างลิงก์ */
            color: black; /* กำหนดสีตัวหนังสือเป็นดำ */
        }

        /* สไตล์เมื่อเม้าส์ hover ที่หนัง */
        .movie a:hover {
            text-decoration: underline; /* เพิ่มเส้นใต้ล่างเมื่อเม้าส์ hover */
        }
        

    </style>
    <style>
        /* สไตล์ CSS ที่ใช้ในหน้า homepage.php */
        body {
            background-color: #000;
            color: #FFF;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        nav {
            background-color: #212121;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #FFF;
            text-decoration: none;
            padding: 10px;
            margin: 5 5px;
        }

        section {
            padding: 10px;
        }

        footer {
            background-color: #212121;
            padding: 10px;
            text-align: center;
        }

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .movie-card {
            background-color: #333;
            color: #FFF;
            padding: 10px;
            margin: 10px;
            text-align: center;
            width: 200px;
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

        /* เมื่อนำเมาส์ไปวัตถุแล้วปรากฏเอฟเฟกต์ */
        .btn-more:hover {
            background-color: #2980b9;
        }

    </style>
</head>
<body>

    <header>
        <h1>S Cinema</h1>
    </header>

    <nav>
        <a href="homepage.php">Home</a>
        <a href="index.php">Movies</a>
        <a href="showtime">Showtimes</a>
        <a href="Promotion">Promotions</a>
    </nav>

    <div class="container">
        <section>
            <div class="image-container" id="slideshow-container">
                <?php
                    // Connection parameters
                    $host = 'localhost';
                    $dbname = 'Scinema';
                    $username = 'root';
                    $password = 'root';

                    // Create connection
                    $conn = new mysqli($host, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query to fetch images with specific PosterID from database
                    $posterIds = ['0000001', '0000002']; // เพิ่ม PosterID ที่ต้องการ
                    foreach ($posterIds as $posterId) {
                        $sql = "SELECT Poster FROM Poster_Promotion WHERE PosterID = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $posterId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Display images
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<img src="data:image/png;base64,' . base64_encode($row['Poster']) . '" />';
                            }
                        }

                        // Close statement
                        $stmt->close();
                    }

                    // Close connection
                    $conn->close();
                ?>
            </div>
        </section>
    </div>

    <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ภาพยนตร์แนะ</h2>
    <div class="container">
        <section class="movie-container">
        <?php
            // Connection parameters
            $host = 'localhost';
            $dbname = 'Scinema';
            $username = 'root';
            $password = 'root';

            // Create connection
            $conn = new mysqli($host, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // คำสั่ง SQL เพื่อดึงข้อมูลภาพยนตร์
            $sql_movies = "SELECT Movie.MovieID, Movie.NameMovie, Movie.ReleaseDate, Genre.Genre, Movie.Poster, Movie.LinkVDO, Movie.LeavingDate 
                        FROM Movie
                        JOIN Genre ON Movie.GenreID = Genre.GenreID
                        WHERE Movie.ReleaseDate > CURDATE() AND (Movie.LeavingDate IS NULL OR Movie.LeavingDate >= CURDATE())";
            $result_movies = $conn->query($sql_movies);


            // แสดงผลภาพยนตร์
            if ($result_movies->num_rows > 0) {
                $count = 0; // ตัวแปรนับจำนวนหนัง
                while ($row_movies = $result_movies->fetch_assoc()) {
                    if ($count % 5 == 0) {
                        echo '</div>'; // ปิดแถวก่อนหนังที่ 6, 11, ...
                        echo '<div class="movie-container">';
                    }
                    echo '<div class="movie-card">';
                    echo '<img src="' . $row_movies["Poster"] . '" alt="' . $row_movies["NameMovie"] . '" style="width: 100%;">';
                    echo '<h3>' . $row_movies["NameMovie"] . '</h3>';
                    echo '<p>วันที่เข้าฉาย: ' . $row_movies["ReleaseDate"] . '</p>';
                    echo '<p>ประเภท: ' . $row_movies["Genre"] . '</p>';
                    echo '<a class="btn-more" href="moviemore.php?movie_id=' . $row_movies["MovieID"] . '">ดูรายละเอียด</a>';
                    echo '</div>';
                    $count++;
                }
                // ปิดแถวสุดท้าย (กรณีที่ไม่ได้ปิดแถวในการวน loop)
                echo '</div>';
            } else {
                echo "No movies available";
            }

            // Close connection
            $conn->close();
            ?>


        </section>
    </div>

    <footer>
        
    </footer>

    <script>
        // JavaScript for automatic slideshow
        const slideshowContainer = document.getElementById('slideshow-container');
        const images = document.querySelectorAll('.image-container img');
        let currentImageIndex = 0;

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            slideshowContainer.scrollLeft = images[currentImageIndex].offsetLeft;
        }

        // Set timeout to wait for 5 seconds before starting slideshow
        setTimeout(function() {
            setInterval(nextImage, 1000); // เลื่อนภาพทุก 1 วินาที
        }, 1000); // 1 seconds timeout before starting
    </script>
</body>
</html>