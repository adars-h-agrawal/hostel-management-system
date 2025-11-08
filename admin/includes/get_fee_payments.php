<?php
require_once('../../db_connection.php');

$query = "
SELECT 
  s.full_name, 
  s.registration_number, 
  f.semester, 
  f.amount, 
  f.status, 
  f.payment_date
FROM fees f
JOIN students s ON f.student_id = s.student_id
ORDER BY f.semester ASC
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
      <td class='px-6 py-3'>{$row['full_name']}</td>
      <td class='px-6 py-3'>{$row['registration_number']}</td>
      <td class='px-6 py-3'>{$row['semester']}</td>
      <td class='px-6 py-3'>₹" . number_format($row['amount'], 2) . "</td>
      <td class='px-6 py-3'>
        <span class='px-2 py-1 rounded-full text-xs " . ($row['status'] == 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') . "'>
          {$row['status']}
        </span>
      </td>
      <td class='px-6 py-3'>" . ($row['payment_date'] ?? '-') . "</td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='6' class='text-center py-4 text-gray-500'>No fee records found.</td></tr>";
}
$conn->close();
?>
