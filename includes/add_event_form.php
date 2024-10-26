<div class="container-fluid mt-4">
    <h1 class="mb-4">Add New Event</h1>

    <form action="events.php?action=add" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Event Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">Please provide an event name.</div>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Select a category</option>
                <option value="Fall">Fall</option>
                <option value="Spring">Spring</option>
                <option value="Summer">Summer</option>
                <option value="Other">Other</option>
            </select>
            <div class="invalid-feedback">Please select a category.</div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Event Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
            <div class="invalid-feedback">Please provide a valid date.</div>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Event Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            <div class="invalid-feedback">Please select an image.</div>
        </div>
        <button type="submit" class="btn btn-primary">Add Event</button>
        <a href="events.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()
</script>