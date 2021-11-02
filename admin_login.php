<?php
session_start();

$submit   = isset($_REQUEST['submit']) && $_REQUEST['submit'] == "submit" ? TRUE                  : FALSE;
$login    = isset($_REQUEST['login'])                                     ? $_REQUEST['login']    : NULL;
$password = isset($_REQUEST['password'])                                  ? $_REQUEST['password'] : NULL;

$invalid_login = false;

if($submit && $login == "nous_admin" && $password == "tdah_6696")
{
    $_SESSION['logged'] = true;
    header("Location: admin_panel.php");
    die();
}
else if($submit)
{
    $invalid_login = true;
}
?>

<!DOCTYPE html>
<html style="height: 100%; width: 100%" lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body style="height: 100%; width: 100%">
    <table style="height: 100%; width: 100%">
        <tbody>
            <tr>
                <td class="align-middle">
                    <div class="row d-flex justify-content-center" style="width: 100% !important;">
                        <div class="card p-4" style="width: 30rem">
                            <div class="card-body">
                                <h5 class="card-title mb-4 p-2 border-bottom border-secondary">NOÛS API Admin Panel</h5>

                                <form method="post">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="label1">Login</span>
                                        <input type="text" name="login" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="label1">
                                    </div>
    
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="label2">Password</span>
                                        <input type="Password" name="password" class="form-control" placeholder="Password" aria-label="Username" aria-describedby="label2">
                                    </div>

                                    <?php
                                        if($invalid_login)
                                            echo "<p class='text-danger'>Invalid Login!</p>";
                                    ?>
    
                                    <button type="submit" name="submit" value="submit" class="btn btn-primary">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>