<?php

// forum.php - Simple PHP Forum
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

// Users must be logged in to view or post
check_login();

// Handle New Topic Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_topic'])) {
    $title = trim($_POST['topic_title']);
    $content = trim($_POST['topic_content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare(
            "INSERT INTO forum_posts (user_email, user_name, post_title, post_content, topic_id) 
             VALUES (:email, :name, :title, :content, 0)"
        );
        $stmt->execute([
            'email' => $_SESSION['email'],
            'name' => $_SESSION['username'],
            'title' => $title,
            'content' => $content
        ]);
        // Redirect to refresh the page
        header("Location: " . BASE_URL . "/forum.php");
        exit;
    }
}

// Fetch all main topics (where topic_id is 0)
$topics = $pdo->query("SELECT * FROM forum_posts WHERE topic_id = 0 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT_PATH . '/includes/header.php';
?>

<section class="container" style="padding-top: 4rem;">
    <h1 style="text-align: center;">Community Forum</h1>
    
    <!-- Form for creating a new topic -->
    <article data-aos="fade-up">
        <header><h4>Create a New Topic</h4></header>
        <form action="<?php echo BASE_URL; ?>/forum.php" method="post">
            <label for="topic_title">Topic Title</label>
            <input type="text" id="topic_title" name="topic_title" required>
            
            <label for="topic_content">Your Message</label>
            <textarea id="topic_content" name="topic_content" rows="4" required></textarea>
            
            <button type="submit" name="submit_topic">Post Topic</button>
        </form>
    </article>

    <!-- List of existing topics -->
    <h3 style="margin-top: 3rem;">All Topics</h3>
    <?php if (empty($topics)): ?>
        <p>No topics found. Be the first to create one!</p>
    <?php else: ?>
        <?php foreach ($topics as $topic): ?>
            <article data-aos="fade-up">
                <header>
                    <a href="<?php echo BASE_URL; ?>/topic.php?id=<?php echo $topic['post_id']; ?>" style="text-decoration: none;">
                        <h5 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($topic['post_title']); ?></h5>
                    </a>
                    <small>
                        Posted by <strong><?php echo htmlspecialchars($topic['user_name']); ?></strong> 
                        on <?php echo date('F j, Y', strtotime($topic['created_at'])); ?>
                    </small>
                </header>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>