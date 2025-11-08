<?php
include '../../db_connection.php';

$sql = "SELECT * FROM students ORDER BY student_id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <tr>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['student_id']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['registration_number']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['full_name']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['email']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['phone']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['block']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['room_number']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".$row['room_type']."</td>
            <td class='px-6 py-4 text-sm text-gray-700'>".date('d M Y', strtotime($row['date_joined']))."</td>
        </tr>
        ";
    }
} else {
    echo "<tr><td colspan='9' class='text-center py-4 text-gray-500'>No students found.</td></tr>";
}

$conn->close();
?>
