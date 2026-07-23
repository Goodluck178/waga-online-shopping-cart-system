<?php
require "config.php";

// Detect if we should prefill fields (only when an error exists)
$prefill = isset($_GET['error']);

if (isset($_POST["signup"])) {
  $name = trim($_POST['name']);
  $surname = trim($_POST['surname']);
  $email = trim($_POST['email']);
  $password = $_POST['pwd'];
  $passwordRepeat = $_POST['pwd-repeat'];
  $mobileNo = trim($_POST['mobileNo']);
  $role = $_POST['role'];

  // 1. Validation
  if (
    empty($name) || empty($surname) || empty($email) || empty($mobileNo) ||
    empty($password) || empty($passwordRepeat) || empty($role)
  ) {
    header("Location: signup.php?error=emptyfields&name=$name&surname=$surname&mail=$email&mobileNo=$mobileNo&role=$role");
    exit();
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=invalidemail&name=$name&surname=$surname&mobileNo=$mobileNo&role=$role");
    exit();
  }

  if ($password !== $passwordRepeat) {
    header("Location: signup.php?error=passwordcheck&name=$name&surname=$surname&mail=$email&mobileNo=$mobileNo&role=$role");
    exit();
  }

  // 2. Unique Email / Mobile Check
  $sql = "SELECT email, mobileNo FROM users WHERE email=? OR mobileNo=?";
  $stmt = mysqli_stmt_init($conn);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: signup.php?error=sqlerror");
    exit();
  }

  mysqli_stmt_bind_param($stmt, "ss", $email, $mobileNo);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {

    if ($row['email'] === $email) {
      header("Location: signup.php?error=emailtaken&name=$name&surname=$surname&mobileNo=$mobileNo&role=$role");
      exit();
    }

    if ($row['mobileNo'] === $mobileNo) {
      header("Location: signup.php?error=mobiletaken&name=$name&surname=$surname&mail=$email&role=$role");
      exit();
    }
  }

  // 3. Insert New User
  $sql = "INSERT INTO users (name, surname, email, password, mobileNo, role)
          VALUES (?, ?, ?, ?, ?, ?)";

  $stmt = mysqli_stmt_init($conn);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: signup.php?error=sqlerror");
    exit();
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  mysqli_stmt_bind_param(
    $stmt,
    "ssssss",
    $name,
    $surname,
    $email,
    $hashedPassword,
    $mobileNo,
    $role
  );

  mysqli_stmt_execute($stmt);

  header("Location: signup.php?signup=success");
  exit();
}

// IMPORTANT: header.php must be loaded AFTER all redirects
require "header.php";
?>

<main class="wrapper-main">

  <h1>Signup</h1>

  <?php
  if (isset($_GET['error'])) {

    if ($_GET['error'] == "emptyfields") {
      echo "<p class='signuperror'>Fill in all fields</p>";
    } else if ($_GET['error'] == "invalidemail") {
      echo "<p class='signuperror'>Invalid email</p>";
    } else if ($_GET['error'] == "passwordcheck") {
      echo "<p class='signuperror'>Passwords do not match</p>";
    } else if ($_GET['error'] == "emailtaken") {
      echo "<p class='signuperror'>Email already taken</p>";
    } else if ($_GET['error'] == "mobiletaken") {
      echo "<p class='signuperror'>Mobile number already taken</p>";
    }
  } else if (isset($_GET['signup']) && $_GET['signup'] == "success") {
    echo "<p class='signupsuccess'>Signup successful</p>";
  }
  ?>
  <div class="ug">
    <div>
      <form action="signup.php" method="post" autocomplete="off">

        <input type="text" name="name" autocomplete="off" placeholder="Name"
          value="<?php echo $prefill && isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">

        <input type="text" name="surname" autocomplete="off" placeholder="Surname"
          value="<?php echo $prefill && isset($_GET['surname']) ? htmlspecialchars($_GET['surname']) : ''; ?>">

        <input type="text" name="email" autocomplete="off" placeholder="E-mail"
          value="<?php echo $prefill && isset($_GET['mail']) ? htmlspecialchars($_GET['mail']) : ''; ?>">

        <input type="tel" name="mobileNo" autocomplete="off" placeholder="Mobile Number"
          value="<?php echo $prefill && isset($_GET['mobileNo']) ? htmlspecialchars($_GET['mobileNo']) : ''; ?>">

        <input type="password" name="pwd" autocomplete="new-password" placeholder="Password">

        <input type="password" name="pwd-repeat" autocomplete="new-password" placeholder="Repeat Password">

        <select name="role" class="input-field">
          <option value="">--Select Role--</option>
          <option value="user" <?php echo $prefill && isset($_GET['role']) && $_GET['role'] == "user" ? "selected" : ""; ?>>User</option>
          <option value="admin" <?php echo $prefill && isset($_GET['role']) && $_GET['role'] == "admin" ? "selected" : ""; ?>>Admin</option>
        </select>

        <button type="submit" name="signup" class="signup-btn">Signup</button>
      </form>
    </div>
  </div>

</main>

<?php
require "footer.php";
?>