<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./assets/css/footer.css" rel="stylesheet">
    <link href="./assets/css/login.css" rel="stylesheet">
    <title>Hệ thống quản lý hồ sơ sinh viên đại học - DNU</title>
</head>

<body>
    <section class="vh-100 d-flex justify-content-center align-items-center">
        <div class="container-fluid h-custom">
            <div class="d-flex flex-row align-items-center justify-content-center">
                <h2 class="login-title text-center mb-4">
                    <i class="fas fa-graduation-cap fa-lg me-3"></i>HỆ THỐNG QUẢN LÝ HỒ SƠ SINH VIÊN ĐẠI HỌC
                </h2>
            </div>
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="./images/login.jpg" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));"
                        alt="Login illustration">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="login-container">
                        <form action="./handle/login_process.php" method="POST">
                            <!-- Username input -->
                            <div class="form-outline mb-4">
                                <label class="form-label" for="username"><i class="fas fa-user me-2"></i>Username</label>
                                <input type="text" name="username" id="username" class="form-control form-control-lg"
                                    placeholder="Nhập username" required />
                            </div>

                            <!-- Password input -->
                            <div class="form-outline mb-3">
                                <label class="form-label" for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                <input type="password" name="password" id="password" class="form-control form-control-lg"
                                    placeholder="Nhập mật khẩu" required />
                        <!-- Thông báo lỗi sử dụng session -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <!-- <div class="d-flex justify-content-between align-items-center">

                            <div class="form-check mb-0">
                                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                                <label class="form-check-label" for="form2Example3">
                                    Remember me
                                </label>
                            </div>
                        </div> -->

                        <div class="text-center mt-4">
                            <button type="submit" name="login" class="btn btn-primary btn-lg btn-block w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<footer class="footer text-center py-3">
    <div class="container">
        <span><i class="far fa-copyright me-2"></i>2025 - FITDNU.</span>
    </div>
</footer>

</html>