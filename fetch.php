<?php
include('db.php');
$limit = 4;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$start = ($page - 1) * $limit;
$qr = isset($_POST['qr']) ? trim($_POST['qr']) : '';

$query = "SELECT * FROM student_data WHERE status='active'";
$total_query = "SELECT COUNT(*) as total FROM student_data WHERE status='active'";
if (!empty($qr)) {
    $query .= " AND (name LIKE :search OR course LIKE :search)";
    $total_query .= " AND (name LIKE :search OR course LIKE :search)";
}

$query .= " LIMIT :start, :limit";

$prepare = $pdo->prepare($query);
$total_prepare = $pdo->prepare($total_query);

if (!empty($qr)) {
    $qrr = "%$qr%";
    $prepare->bindParam(':search', $qrr, PDO::PARAM_STR);
    $total_prepare->bindParam(':search', $qrr, PDO::PARAM_STR);
}

$prepare->bindParam(':start', $start, PDO::PARAM_INT);
$prepare->bindParam(':limit', $limit, PDO::PARAM_INT);

$prepare->execute();
$total_prepare->execute();

$fetch = $prepare->fetchAll(PDO::FETCH_ASSOC);
$total = $total_prepare->fetch(PDO::FETCH_ASSOC)['total'];

if (count($fetch) == 0) {
    echo json_encode(['content' => '<p class="text-center text-gray-500 mt-4">No Data Found</p>', 'pagination' => '']);
    exit;
}

$content = '<div class="overflow-x-auto text-[15px]">
<table class="border border-gray-300 w-full">
<thead>
<tr class="bg-gray-200">
<th class="border py-3 px-3">Name</th>
<th class="border py-3 px-3">Email</th>
<th class="border py-3 px-3">Phone</th>
<th class="border py-3 px-3">Course</th>
<th class="border py-3 px-3">Date</th>
<th class="border py-3 px-3">Status</th>
</tr>
</thead><tbody>';

foreach ($fetch as $value) {
    $content .= '
    <tr class="hover:bg-gray-100">
    <td class="border py-3 px-3">' . htmlspecialchars($value["name"]) . '</td>
    <td class="border py-3 px-3">' . htmlspecialchars($value["email"]) . '</td>
    <td class="border py-3 px-3">' . htmlspecialchars($value["phone"]) . '</td>
    <td class="border py-3 px-3">' . htmlspecialchars($value["course"]) . '</td>
    <td class="border py-3 px-3">' . htmlspecialchars($value["date"]) . '</td>
    <td class="border py-3 px-3 text-green-600 font-semibold">' . htmlspecialchars($value["status"]) . '</td>
    </tr>';
}

$content .= '</tbody></table></div>';

$total_pages = ceil($total / $limit);
$pagination = '<div class="flex justify-center space-x-2">';

for ($i = 1; $i <= $total_pages; $i++) {
    $pagination .= '<button class="page-link bg-[black] text-white px-3 py-2 rounded-lg" data-page="' . $i . '">' . $i . '</button>';
}
$pagination .= '</div>';

echo json_encode(['content' => $content, 'pagination' => $pagination]);
?>
