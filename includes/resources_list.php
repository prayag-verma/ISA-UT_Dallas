<h2>Resources</h2>
<a href="resources.php?action=add" class="btn btn-primary mb-3">Add New Resource</a>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Icon</th>
            <th>Description</th>
            <th>More Info</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resources as $resource): ?>
        <tr>
            <td><i class="<?php echo htmlspecialchars($resource['icon']); ?>"></i></td>
            <td><?php echo htmlspecialchars($resource['description']); ?></td>
            <td><a href="<?php echo htmlspecialchars($resource['more_info_url']); ?>" target="_blank"><?php echo htmlspecialchars($resource['more_info_text']); ?></a></td>
            <td>
                <a href="resources.php?action=edit&id=<?php echo $resource['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <form action="resources.php?action=delete" method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this resource?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>