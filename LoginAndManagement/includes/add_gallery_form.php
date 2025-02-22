<h2>Add New Gallery Item</h2>
<form action="gallery.php?action=add" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="year" class="form-label">Year</label>
        <input type="text" class="form-control" id="year" name="year" required pattern="\d{4}-\d{4}">
        <small class="form-text text-muted">Format: YYYY-YYYY (e.g., 2023-2024)</small>
    </div>
    <div class="mb-3">
        <label for="semester" class="form-label">Semester</label>
        <select class="form-select" id="semester" name="semester" required>
            <option value="Fall">Fall</option>
            <option value="Spring">Spring</option>
            <option value="Summer">Summer</option>
            <option value="Other">Other</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
    </div>
    <div class="mb-3">
        <label for="order" class="form-label">Display Order</label>
        <input type="number" class="form-control" id="order" name="order" required min="1">
    </div>
    <button type="submit" class="btn btn-primary">Add Gallery Item</button>
</form>