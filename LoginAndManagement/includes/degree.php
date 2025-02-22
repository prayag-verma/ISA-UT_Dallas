<h5 class="mt-3"><span class="badge bg-primary"> Total Degrees : <?php echo $totalDegrees; ?> </span></h5>
<form method="post" class="mb-4">
    <div class="input-group">
        <input type="text" name="degree" class="form-control" placeholder="Enter New Degree Name..." required>
        <button type="submit" name="add_degree" class="btn btn-primary">Add Degree</button>
    </div>
</form>
<ul class="list-group">
    <?php foreach ($degrees as $degree): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo htmlspecialchars($degree['name']); ?>
            <form method="post" class="d-inline">
                <input type="hidden" name="degree_id" value="<?php echo $degree['id']; ?>">
                <button type="submit" name="delete_degree" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Pagination -->
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPagesDegrees; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="isa_settings.php?tab=degree&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>