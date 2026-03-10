<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTCI Student Council Election - Election Creation</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/global.css" />
    <link rel="stylesheet" href="../../assets/css/form.css" />
    <link rel="stylesheet" href="../../assets/css/status.css" />
    <link rel="stylesheet" href="../../assets/css/navbar.css" />
    <link rel="stylesheet" href="../../assets/css/theme.css" />
    <link rel="website icon" type="png" sizes="32x32" href="../../img/logo/PTCI-logo.png">
</head>
<body class="form-body">

    <!-- Navigation -->
    <?php include '../components/navigationbar.php'; ?>

    <!-- Floating Logos Background -->
    <div class="form-floating-logos" id="formFloatingLogos"></div>

    <div class="form-container">
        <div class="form-card">

            <!-- Logo Section -->
            <div class="form-logo">
                <img src="../../img/logo/PTCI-logo.png" alt="PTCI Logo">
            </div>

            <!-- Title -->
            <div class="form-header">
                <h1>Election Setup</h1>
                <p>Set up a new election for the PTCI Student Council</p>
            </div>

            <!-- Error Alert -->
            <div class="form-alert" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i>
                <span id="errorMessage">An error occurred. Please try again.</span>
            </div>

            <!-- Registration Form -->
            <form id="election-form">
                <div class="form-group">
                    <label for="elec_name">Election Name</label>
                    <input type="text" name="ElectionName" id="elec_name" class="form-control" placeholder="Enter Election Name" required />
                </div>

                <div class="form-group">
                    <label for="schoolYear">School Year</label>
                    <input type="text" name="SchoolYear" id="schoolYear" class="form-control" placeholder="Enter School Year" required />
                </div>

                <button type="submit" class="form-btn-submit" id="submitBtn">
                    <i class="fa-regular fa-calendar-plus"></i>
                     Submit
                </button>
            </form>

        </div>
    </div>
</body>
</html>