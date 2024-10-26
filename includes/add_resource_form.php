<h2>Add New Resource</h2>
<form action="resources.php?action=add" method="post">
    <div class="mb-3">
        <label for="icon" class="form-label">Icon</label>
        <input type="text" class="form-control" id="icon" name="icon" required>
        <small class="form-text text-muted">Enter a Font Awesome icon class (e.g., 'fas fa-book')</small>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="more_info_text" class="form-label">More Info Text</label>
        <input type="text" class="form-control" id="more_info_text" name="more_info_text" required>
    </div>
    <div class="mb-3">
        <label for="more_info_url" class="form-label">More Info URL</label>
        <input type="url" class="form-control" id="more_info_url" name="more_info_url" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Resource</button>
</form>