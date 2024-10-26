<h2>Edit Gallery Item</h2>
<form action="gallery.php?action=edit" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $galleryItem['id']; ?>">
    <div class="mb-3">
        <label for="year" class="form-label">Year</label>
        <input type="text" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($galleryItem['year']); ?>" required pattern="\d{4}-\d{4}">
        <small class="form-text text-muted">Format: YYYY-YYYY (e.g., 2023-2024)</small>
    </div>
    <div class="mb-3">
        <label for="semester" class="form-label">Semester</label>
        <select class="form-select" id="semester" name="semester" required>
            <option value="Fall" <?php echo $galleryItem['semester'] == 'Fall' ? 'selected' : ''; ?>>Fall</option>
            <option value="Spring" <?php echo $galleryItem['semester'] == 'Spring' ? 'selected' : ''; ?>>Spring</option>
            <option value="Summer" <?php echo $galleryItem['semester'] == 'Summer' ? 'selected' : ''; ?>>Summer</option>
            <option value="Other" <?php echo $galleryItem['semester'] == 'Other' ? 'selected' : ''; ?>>Other</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        <small class="form-text text-muted">Leave blank to keep the current image.</small>
    </div>
    <div class="mb-3">
        <label for="order" class="form-label">Display Order</label>
        <input type="number" class="form-control" id="order" name="order" value="<?php echo $galleryItem['order']; ?>" required min="1">
    </div>
    <button type="submit" class="btn btn-primary">Update Gallery Item</button>
</form>