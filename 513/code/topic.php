<?php
// topic.php - View a single topic and its replies
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

check_login();

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($topic_id === 0) {
    header("Location: " . BASE_URL . "/forum.php");
    exit;
}

// Handle New Reply Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $content = trim($_POST['reply_content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare(
            "INSERT INTO forum_posts (user_email, user_name, post_content, topic_id) 
             VALUES (:email, :name, :content, :topic_id)"
        );
        $stmt->execute([
            'email' => $_SESSION['email'],
            'name' => $_SESSION['username'],
            'content' => $content,
            'topic_id' => $topic_id
        ]);
        header("Location: " . BASE_URL . "/topic.php?id=" . $topic_id);
        exit;
    }
}

// Fetch the main topic post
$stmt_topic = $pdo->prepare("SELECT * FROM forum_posts WHERE post_id = ? AND topic_id = 0");
$stmt_topic->execute([$topic_id]);
$topic = $stmt_topic->fetch(PDO::FETCH_ASSOC);

// If topic doesn't exist, redirect
if (!$topic) {
    header("Location: " . BASE_URL . "/forum.php");
    exit;
}

// Fetch all replies for this topic
$replies = $pdo->prepare("SELECT * FROM forum_posts WHERE topic_id = ? ORDER BY created_at ASC");
$replies->execute([$topic_id]);
$all_replies = $replies->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT_PATH . '/includes/header.php';
?>

<section class="container" style="padding-top: 4rem;">
    <!-- Main Topic Display -->
    <article data-aos="fade-up" style="border: 2px solid var(--primary);">
        <header>
            <h1><?php echo htmlspecialchars($topic['post_title']); ?></h1>
            <small>
                Posted by <strong><?php echo htmlspecialchars($topic['user_name']); ?></strong> 
                on <?php echo date('F j, Y', strtotime($topic['created_at'])); ?>
            </small>
        </header>
        <p><?php echo nl2br(htmlspecialchars($topic['post_content'])); ?></p>
    </article>

    <!-- Replies Display -->
    <h4 style="margin-top: 3rem;">Replies</h4>
    <?php if (empty($all_replies)): ?>
        <p>No replies yet. Be the first to respond!</p>
    <?php else: ?>
        <?php foreach ($all_replies as $reply): ?>
            <article data-aos="fade-up" style="margin-bottom: 1rem;">
                <p><?php echo nl2br(htmlspecialchars($reply['post_content'])); ?></p>
                <footer>
                    <small>
                        Replied by <strong><?php echo htmlspecialchars($reply['user_name']); ?></strong> 
                        on <?php echo date('F j, Y', strtotime($reply['created_at'])); ?>
                    </small>
                </footer>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Reply Form -->
    <article data-aos="fade-up" style="margin-top: 3rem;">
        <header><h5>Post a Reply</h5></header>
        <form action="<?php echo BASE_URL; ?>/topic.php?id=<?php echo $topic_id; ?>" method="post">
            <textarea name="reply_content" rows="5" required></textarea>
            <button type="submit" name="submit_reply">Submit Reply</button>
        </form>
    </article>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>