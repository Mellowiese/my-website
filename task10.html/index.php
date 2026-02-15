<?php
// Include database connection
require_once 'connection.php';

$message = '';
$message_type = '';

// Handle ADD entry
if (isset($_POST['add_entry'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO journal (title, content) VALUES (:title, :content)");
        $stmt->execute([
            ':title' => $_POST['title'],
            ':content' => $_POST['content']
        ]);
        $message = "Entry added successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Handle UPDATE entry
if (isset($_POST['update_entry'])) {
    try {
        $stmt = $conn->prepare("UPDATE journal SET title = :title, content = :content WHERE id = :id");
        $stmt->execute([
            ':title' => $_POST['title'],
            ':content' => $_POST['content'],
            ':id' => $_POST['id']
        ]);
        $message = "Entry updated successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Handle DELETE entry
if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM journal WHERE id = :id");
        $stmt->execute([':id' => $_GET['delete']]);
        $message = "Entry deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Get entry for editing
$edit_entry = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM journal WHERE id = :id");
    $stmt->execute([':id' => $_GET['edit']]);
    $edit_entry = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Retrieve all entries
$stmt = $conn->query("SELECT * FROM journal ORDER BY entry_date DESC");
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Journal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 800px;">
        
        <h1 class="text-center mb-4">📖 My Journal</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Entry Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5><?= $edit_entry ? 'Edit Entry' : 'New Entry' ?></h5>
                <form method="POST">
                    <?php if ($edit_entry): ?>
                        <input type="hidden" name="id" value="<?= $edit_entry['id'] ?>">
                    <?php endif; ?>
                    
                    <input type="text" name="title" class="form-control mb-3" 
                           placeholder="Title" required
                           value="<?= $edit_entry ? htmlspecialchars($edit_entry['title']) : '' ?>">
                    
                    <textarea name="content" class="form-control mb-3" 
                              placeholder="What's on your mind?" rows="5" required><?= $edit_entry ? htmlspecialchars($edit_entry['content']) : '' ?></textarea>
                    
                    <?php if ($edit_entry): ?>
                        <button type="submit" name="update_entry" class="btn btn-warning">Update Entry</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_entry" class="btn btn-primary">Save Entry</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Display Entries -->
        <h4 class="mb-3">All Entries</h4>
        <?php if (count($entries) > 0): ?>
            <?php foreach ($entries as $entry): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($entry['title']) ?></h5>
                        <small class="text-muted">
                            <?= date('M j, Y - g:i A', strtotime($entry['entry_date'])) ?>
                        </small>
                        <p class="mt-2"><?= nl2br(htmlspecialchars($entry['content'])) ?></p>
                        <a href="?edit=<?= $entry['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $entry['id'] ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Delete this entry?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No entries yet. Start writing!</div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>