<?php
// recruitment.php - Job Application with File Upload
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/header.php';

$success_msg = "";
$error_msg = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $position = trim($_POST['position']);
    
    // File Upload Logic
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cv_file']['tmp_name'];
        $fileName = $_FILES['cv_file']['name'];
        $fileSize = $_FILES['cv_file']['size'];
        $fileType = $_FILES['cv_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Allowed extensions
        $allowedfileExtensions = array('pdf', 'doc', 'docx');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where CVs will be saved
            $uploadFileDir = ROOT_PATH . '/assets/uploads/';
            
            // Ensure directory exists
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // File moved successfully, now save to DB
                try {
                    $stmt = $pdo->prepare("INSERT INTO job_applications (full_name, email, position, cv_file_path) VALUES (:name, :email, :pos, :path)");
                    $stmt->execute([
                        'name' => $name,
                        'email' => $email,
                        'pos' => $position,
                        'path' => 'assets/uploads/' . $newFileName
                    ]);
                    $success_msg = "Application received! Good luck.";
                } catch (Exception $e) {
                    $error_msg = "Database error: " . $e->getMessage();
                }
            } else {
                $error_msg = "Error moving the file to upload directory. Check folder permissions.";
            }
        } else {
            $error_msg = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    } else {
        $error_msg = "Please upload a CV file (PDF or Word).";
    }
}
?>

<!-- Hero Section -->
<section style="text-align: center; padding: 6rem 0; background-color: var(--bg-mint);">
    <h1 data-aos="fade-down">Join Our Team</h1>
    <p data-aos="fade-up" data-aos-delay="100">Help us build a more sustainable future.</p>
</section>

<section class="container" style="margin-top: 4rem;">
    
    <?php if ($success_msg): ?>
        <article style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; text-align: center;">
            <h4>✅ <?php echo $success_msg; ?></h4>
            <p>We will review your application and get back to you if your profile matches.</p>
            <a href="<?php echo BASE_URL; ?>/" role="button" class="contrast">Back Home</a>
        </article>
    <?php else: ?>

        <div class="grid" style="align-items: start; gap: 3rem;">
            
            <!-- Open Positions List -->
            <div data-aos="fade-right">
                <h3 style="color: var(--primary);">Open Positions</h3>
                
                <article style="margin-bottom: 1rem; padding: 1.5rem;">
                    <header><strong>📦 Logistics Coordinator</strong></header>
                    <p style="font-size: 0.9rem;">Manage our eco-friendly supply chain and ensure plastic-free shipping standards.</p>
                    <small style="color: var(--text-light);">Melbourne, AU (On-site)</small>
                </article>

                <article style="margin-bottom: 1rem; padding: 1.5rem;">
                    <header><strong>📸 Content Creator</strong></header>
                    <p style="font-size: 0.9rem;">Create engaging social media content about zero-waste living.</p>
                    <small style="color: var(--text-light);">Remote</small>
                </article>

                <article style="padding: 1.5rem;">
                    <header><strong>💻 Junior Web Developer</strong></header>
                    <p style="font-size: 0.9rem;">Help maintain and improve our PHP/WordPress hybrid platform.</p>
                    <small style="color: var(--text-light);">Remote / Hybrid</small>
                </article>
            </div>

            <!-- Application Form -->
            <div data-aos="fade-left">
                <article>
                    <header><h4 style="margin-bottom:0;">Apply Now</h4></header>
                    
                    <?php if ($error_msg): ?>
                        <p style="color: red; font-size: 0.9rem;"><?php echo $error_msg; ?></p>
                    <?php endif; ?>

                    <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
                    <form action="<?php echo BASE_URL; ?>/recruitment.php" method="post" enctype="multipart/form-data">
                        <label>
                            Full Name
                            <input type="text" name="full_name" required>
                        </label>

                        <label>
                            Email Address
                            <input type="email" name="email" required>
                        </label>

                        <label>
                            Position
                            <select name="position">
                                <option value="Logistics Coordinator">Logistics Coordinator</option>
                                <option value="Content Creator">Content Creator</option>
                                <option value="Web Developer">Junior Web Developer</option>
                                <option value="General Application">General Application</option>
                            </select>
                        </label>

                        <label>
                            Upload CV (PDF, DOCX - Max 2MB)
                            <input type="file" name="cv_file" required accept=".pdf,.doc,.docx">
                        </label>

                        <button type="submit">Submit Application</button>
                    </form>
                </article>
            </div>

        </div>

    <?php endif; ?>

</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>