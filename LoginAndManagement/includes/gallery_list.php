<h2>Gallery</h2>
<a href="gallery.php?action=add" class="btn btn-primary mb-3">Add New Gallery Item</a>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Year</th>
            <th>Semester</th>
            <th>Image</th>
            <th>Order</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($galleryItems as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['year']); ?></td>
            <td><?php echo htmlspecialchars($item['semester']); ?></td>
            <td><img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Gallery Image" style="max-width: 100px; max-height: 100px;"></td>
            <td><?php echo $item['order']; ?></td>
            <td>
                <a href="gallery.php?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <form action="gallery.php?action=delete" method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this gallery item?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>