<br></br>
<h5 class="mt-3"><span class="badge bg-primary"> Total Positions : <?php echo $totalPositions; ?> </span></h5>
<form method="post" class="mb-4">
    <div class="input-group">
        <input type="text" name="position" class="form-control" placeholder="Enter New Position Name..." required>
        <button type="submit" name="add_position" class="btn btn-primary">Add New Position</button>
    </div>
</form>
<ul class="list-group">
    <?php foreach ($positions as $position): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo htmlspecialchars($position['name']); ?>
            <form method="post" class="d-inline">
                <input type="hidden" name="position_id" value="<?php echo $position['id']; ?>">
                <button type="submit" name="delete_position" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Pagination -->
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?tab=officer_position&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>