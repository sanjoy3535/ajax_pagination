<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=student_management_system", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p class="text-center text-red-500 font-bold text-lg">Database connection failed!</p>');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-gray-100 text-[15px]">
    <div class='max-w-3xl mx-auto mt-10  shadow-md p-6 rounded-lg '>
        <div class='bg-red-600 py-[20px] rounded-lg'>
        <h1 class="text-2xl font-bold text-center mb-4 text-white">Student Management System</h1>
        
        <center><input type="text" id="search_btn" placeholder="Search student..." 
        class="border border-gray-300 w-[400px] p-3 rounded-lg   mx-auto outline-none shadow-lgd"></center>
</div>
        <div id='student_data' class="mt-4"></div>
        <div id="pagination" class="flex justify-center space-x-2 mt-4"></div>
    </div>
    <script>
    $(document).ready(function() {
    function fetchData(page = 1, query = '') {
        $.ajax({
            url: 'fetch.php',
            type: 'POST',
            data: { page: page, qr: query },
            success: function(response) {
                if (!response.trim()) { 
                    $('#student_data').html('<p class="text-center text-gray-500 mt-4">No Data Found</p>');
                    $('#pagination').html('');
                } else {
                    let data = JSON.parse(response);
                    $('#student_data').html(data.content);
                    $('#pagination').html(data.pagination);
                }
            },
            error: function() {
                alert('Error fetching data.');
            }
        });
    }

    fetchData();
    $(document).on('input', '#search_btn', function() {
        let query = $(this).val();
        fetchData(1, query);
    });
    $(document).on('click', '.page-link', function() {
        let page = $(this).data('page');
        let query = $('#search_btn').val();
        fetchData(page, query);
    });
});

    </script>
</body>
</html>
