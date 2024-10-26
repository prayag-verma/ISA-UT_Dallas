<div class="container-fluid mt-4">
    <h1 class="mb-4">Manage Events</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="events.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Event
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                    <td><?php echo htmlspecialchars($event['category']); ?></td>
                    <td><?php echo date('F d, Y', strtotime($event['date'])); ?></td>
                    <td>
                        <a href="events.php?action=edit&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="events.php?action=delete" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>