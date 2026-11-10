<?php
require 'includes/config.php';
$action   = $_GET['action'] ?? 'login';
$redirect = $_GET['redirect'] ?? 'glasses.php';
$err = ''; $pending = false;

if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $act = $_POST['act'] ?? 'login';

  // ── LOGIN ──────────────────────────────────────────────────────────────────
  if($act === 'login'){
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $st->execute([trim($_POST['email'])]);
    $u = $st->fetch();

    if($u && password_verify($_POST['password'], $u['password_hash'])){
      // Check approval status
      $status = $u['status'] ?? 'Active';
      if($status === 'Pending'){
        $pending = true; // show pending popup
      } elseif($status === 'Suspended'){
        $err = 'Your account has been suspended. Please contact us.';
      } else {
        // Active — log in
        session_regenerate_id(true);
        $_SESSION['user_id']   = $u['id'];
        $_SESSION['user_name'] = $u['full_name'];
        header('Location:'.htmlspecialchars($redirect)); exit;
      }
    } else {
      $err = 'Invalid email or password.';
    }
    $action = 'login';
  }

  // ── REGISTER ───────────────────────────────────────────────────────────────
  if($act === 'register'){
    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'];
    $pass2 = $_POST['password2'];

    if(strlen($pass) < 6){
      $err = 'Password must be at least 6 characters.'; $action = 'register';
    } elseif($pass !== $pass2){
      $err = 'Passwords do not match.'; $action = 'register';
    } else {
      $chk = $pdo->prepare('SELECT id FROM users WHERE email=?');
      $chk->execute([$email]);
      if($chk->fetch()){
        $err = 'An account with this email already exists.'; $action = 'register';
      } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $ins  = $pdo->prepare('INSERT INTO users(full_name,email,phone,password_hash,status) VALUES(?,?,?,?,?)');
        $ins->execute([$name, $email, $phone, $hash, 'Pending']);
        // Show pending message — do NOT log them in yet
        $action  = 'registered_pending';
      }
    }
  }
}

$pageTitle = match($action){
  'register'          => 'Create Account',
  'registered_pending'=> 'Account Created',
  default             => 'Sign In'
};
include 'includes/header.php';
?>

<section class="section soft d-flex align-items-center" style="min-height:80vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-9 col-lg-6">

        <?php if($action === 'registered_pending'): ?>
        <!-- ── PENDING APPROVAL SCREEN ───────────────────────────────────── -->
        <div class="cardx p-4 p-lg-5 text-center">
          <div style="font-size:3.5rem">⏳</div>
          <h3 class="fw-bold mt-3">Account Created!</h3>
          <p class="text-muted fs-5 mt-2">
            Your account has been submitted and is <b>awaiting approval</b> from the Lapaire team.
          </p>
          <div class="pending-info-box mt-4">
            <div class="fw-semibold mb-1">What happens next?</div>
            <ul class="text-start text-muted small mb-0 ps-3">
              <li>Our team will review and approve your account</li>
              <li>This usually takes less than 24 hours</li>
              <li>Once approved you can log in and start saving frames</li>
            </ul>
          </div>
          <p class="text-muted small mt-4">
            Questions? Contact us at
            <a href="contact.php" class="text-primary fw-semibold">lapaire.ug</a>
            or call <b>+256 775 488 056</b>
          </p>
          <a href="index.php" class="sl-btn sl-btn--primary d-inline-block mt-3">Back to Home</a>
        </div>

        <?php elseif($pending): ?>
        <!-- ── PENDING LOGIN ATTEMPT ─────────────────────────────────────── -->
        <div class="cardx p-4 p-lg-5 text-center">
          <div style="font-size:3.5rem">🔒</div>
          <h3 class="fw-bold mt-3">Account Pending Approval</h3>
          <p class="text-muted fs-5 mt-2">
            Your account is still waiting for approval from our team.<br>
            You'll be able to log in once it's approved.
          </p>
          <div class="pending-info-box mt-4">
            <div class="fw-semibold">Typical approval time: under 24 hours</div>
            <div class="text-muted small mt-1">📞 +256 775 488 056 &nbsp;|&nbsp; ✉️ hello@lapaire.ug</div>
          </div>
          <a href="user_auth.php?action=login&redirect=<?=urlencode($redirect)?>"
             class="sl-btn sl-btn--primary d-inline-block mt-4">Try Again</a>
          <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Go Home</a>
        </div>

        <?php else: ?>
        <!-- ── AUTH TABS ─────────────────────────────────────────────────── -->
        <div class="auth-tabs mb-4">
          <a href="?action=login&redirect=<?=urlencode($redirect)?>"
             class="auth-tab <?=$action==='login'?'active':''?>">Sign In</a>
          <a href="?action=register&redirect=<?=urlencode($redirect)?>"
             class="auth-tab <?=$action==='register'?'active':''?>">Create Account</a>
        </div>

        <div class="cardx p-4 p-lg-5">
          <?php if($err):?>
          <div class="alert alert-danger rounded-3"><?=e($err)?></div>
          <?php endif;?>

          <?php if($action==='login'): ?>
          <!-- LOGIN FORM -->
          <h3 class="fw-bold mb-1">Welcome back</h3>
          <p class="text-muted mb-4">Sign in to access your wishlist and saved frames.</p>
          <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <input type="hidden" name="act"  value="login">
            <div class="col-12">
              <label class="form-label fw-semibold">Email Address</label>
              <input required type="email" name="email" class="form-control" placeholder="you@example.com" autocomplete="email">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Password</label>
              <input required type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="current-password">
            </div>
            <div class="col-12 mt-2">
              <button class="sl-btn sl-btn--primary w-100 text-center">Sign In &rarr;</button>
            </div>
          </form>
          <p class="text-center mt-4 text-muted small">
            No account yet?
            <a href="?action=register&redirect=<?=urlencode($redirect)?>" class="text-primary fw-semibold">Create one free</a>
          </p>

          <?php else: ?>
          <!-- REGISTER FORM -->
          <h3 class="fw-bold mb-1">Create your account</h3>
          <p class="text-muted mb-4">Save frames, track eye care and get personalised help.</p>
          <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?=csrf()?>">
            <input type="hidden" name="act"  value="register">
            <div class="col-12">
              <label class="form-label fw-semibold">Full Name</label>
              <input required name="full_name" class="form-control" placeholder="John Doe">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address</label>
              <input required type="email" name="email" class="form-control" placeholder="you@example.com">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone <small class="text-muted">(optional)</small></label>
              <input name="phone" class="form-control" placeholder="+256 ...">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Password</label>
              <input required type="password" name="password" class="form-control" placeholder="Min 6 characters">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Confirm Password</label>
              <input required type="password" name="password2" class="form-control" placeholder="Repeat password">
            </div>
            <div class="col-12 mt-2">
              <button class="sl-btn sl-btn--primary w-100 text-center">Create Account &rarr;</button>
            </div>
          </form>
          <p class="text-center mt-4 text-muted small">
            Already have an account?
            <a href="?action=login&redirect=<?=urlencode($redirect)?>" class="text-primary fw-semibold">Sign in</a>
          </p>
          <?php endif;?>
        </div>
        <?php endif;?>

      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php';?>
