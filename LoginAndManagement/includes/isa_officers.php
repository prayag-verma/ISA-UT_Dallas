<?php
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalOfficers = getTotalActiveOfficers($conn);
$totalPages = ceil($totalOfficers / 10);
$activeOfficers = getActiveOfficers($conn, $page);
$totalActiveOfficers = getTotalActiveOfficers($conn);
$getTotalDisabledOfficers = getTotalDisabledOfficers($conn);
$totalRetiredOfficers = getTotalRetiredOfficers($conn);
?>

<h3 class="mt-3">ISA Officers</h3>
<p class="mt-3"><span class="badge bg-success"> Active Officers : <?php echo $totalActiveOfficers; ?> </span></p>
<p class="mt-3"><span class="badge bg-warning"> Inactive Officers : <?php echo $getTotalDisabledOfficers; ?> </span></p>
<p class="mt-3"><span class="badge bg-danger"> Retired Officers : <?php echo $totalRetiredOfficers; ?> </span></p>
<table class="table table-striped">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>D.O.B</th>
            <th>Officer Position</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Degree</th>
            <th>Major</th>
            <th>Joining Date</th>
            <th>Expected Grad Date</th>
            <th>Officer's Points</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($activeOfficers as $officer): ?>
        <tr>
            <td><?php echo htmlspecialchars($officer['first_name']); ?></td>
            <td><?php echo htmlspecialchars($officer['last_name']); ?></td>
            <td><?php echo htmlspecialchars($officer['dob']); ?></td>
            <td><?php echo htmlspecialchars($officer['position']); ?></td>
            <td><?php echo htmlspecialchars(strtolower($officer['email'])); ?></td>
            <td><?php echo htmlspecialchars($officer['mobile']); ?></td>
            <td><?php echo htmlspecialchars($officer['degree']); ?></td>
            <td><?php echo htmlspecialchars($officer['major']); ?></td>
            <td><?php echo htmlspecialchars($officer['joining_date']); ?></td>
            <td><?php echo htmlspecialchars($officer['expected_grad_date']); ?></td>
            <td><?php echo htmlspecialchars($officer['officer_ranking']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Pagination -->
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="?tab=isa-officers&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>