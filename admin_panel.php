<?php
session_start();

require "conn.php";
require "api.php";

$logoff     = isset($_REQUEST['logoff'])     ? $_REQUEST['logoff']     : NULL;

$action     = isset($_REQUEST['action'])     ? $_REQUEST['action']     : NULL;
$app_name   = isset($_REQUEST['app_name'])   ? $_REQUEST['app_name']   : NULL;
$disabled   = isset($_REQUEST['disabled'])   ? $_REQUEST['disabled']   : NULL;
$key_id     = isset($_REQUEST['key_id'])     ? $_REQUEST['key_id']     : NULL;

if(!empty($logoff))
    unset($_SESSION['logged']);

if(!(isset($_SESSION['logged']) && $_SESSION['logged'] == true))
{
    header("Location: admin_login.php");
    die();
}

switch ($action) {
    case 'add':
        if(!empty($app_name))
        {
            $disabled = !empty($disabled) && $disabled == "on" ? 1 : 0;
            $api_key = base64_encode(md5(uniqid(rand(), true)));

            $ret = addApiKey($api_key, $app_name, $disabled);

            header("Location: admin_panel.php");
            die();
        }

        header("Location: admin_panel.php");
        die();
        break;
    
    case 'remove':
        if(is_numeric($key_id))
        {
            $ret = removeApiKey($key_id);
            header("Location: admin_panel.php");
            die();
        }

        header("Location: admin_panel.php");
        die();
        break;

    case 'disable':
        if(is_numeric($key_id))
        {
            $ret = disableEnableApiKey($key_id, 1);
            header("Location: admin_panel.php");
            die();
        }

        header("Location: admin_panel.php");
        die();
        break;

    case 'enable':
        if(is_numeric($key_id))
        {
            $ret = disableEnableApiKey($key_id, 0);
            header("Location: admin_panel.php");
            die();
        }
        break;

    default:
        $api_keys = listApiKeys();
        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">

    <style>
        .btn-icon{
            background-color : transparent;
            border : 0;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <form method="post">
                            <button type="submit" name="logoff" value="logoff" class="btn btn-danger" aria-current="page">Logoff</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="add-key" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="form-text">
                        <h5 class="modal-title text-dark">Add API Key</h5>

                        The key is automatically created.
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="app-name" class="form-label">APP Name</label>
                            <input type="text" name="app_name" class="form-control" id="app-name" required>
                            <div class="form-text">What APP will use this key?</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="disabled" class="form-check-input" id="disabled">
                            <label class="form-check-label" for="disabled">Disabled?</label>
                        </div>
        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="action" value="add" class="btn btn-success">Add Key</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="remove-key" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">Remove API Key</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to remove this key?</p>
                    <form method="post">
                        <input type="hidden" name="key_id" id="remove-key-id" value="">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="action" value="remove" class="btn btn-danger">Remove Key</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="disable-key" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">Disable API Key</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to disable this key?</p>
                    <form method="post">
                        <input type="hidden" name="key_id" id="disable-key-id" value="">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="action" value="disable" class="btn btn-warning">Disable Key</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="enable-key" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">Enable API Key</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to enable this key?</p>
                    <form method="post">
                        <input type="hidden" name="key_id" id="enable-key-id" value="">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="action" value="enable" class="btn btn-success">Enable Key</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container p-3">
        <h2 class="mt-3">
            API Keys
        </h2>

        <table class="table table-striped table-bordered mt-2">
            <thead>
                <th>
                    API Key
                </th>

                <th>
                    APP Name
                </th>

                <th>
                    Configs
                </th>
            </thead>

            <tbody>
                <?php
                if(count($api_keys) > 0)
                {
                    foreach($api_keys as $key)
                    {
                ?>

                    <tr <?php echo $key['disabled'] == 1 ? "class='table-danger'" : "" ?>>
                        <td>
                            <?php echo $key['api_key'] ?>
                        </td>
        
                        <td>
                            <?php echo $key['app_name'] ?>
                        </td>

                        <td class="pb-0">
                            <div class="row justify-content-center">
                                <div class="col-3">
                                    <button class="btn-icon <?php echo $key['disabled'] == 1 ? "enable-key" : "disable-key" ?>" data-id="<?php echo $key['id'] ?>" data-bs-toggle="modal" data-bs-target="#<?php echo $key['disabled'] == 1 ? "enable-key" : "disable-key" ?>">
                                        <?php
                                        if($key['disabled'] == 1)
                                            echo "<span class='material-icons text-success fs-4' title='Enable this Key'>lock_open</span>";
                                        else
                                            echo "<span class='material-icons-outlined text-warning fs-4' title='Disable this Key'>lock</span>";
                                        ?>
                                    </button>
                                </div>

                                <div class="col-3">
                                    <button class="btn-icon remove-key" data-id="<?php echo $key['id'] ?>" data-bs-toggle="modal" data-bs-target="#remove-key">
                                        <span class="material-icons text-danger fs-4" title="Delete this Key">delete</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                <?php
                    }
                }
                else
                {
                ?>

                    <tr>
                        <td colspan="3">
                            <p class="text-center mb-0">No keys found</p>
                        </td>
                    </tr>

                <?php
                }
                ?>
            </tbody>
        </table>

        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#add-key">Add</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script>
        $(".remove-key").click(function(){
            $("#remove-key-id").val($(this).data("id"));
        });

        $(".disable-key").click(function(){
            $("#disable-key-id").val($(this).data("id"));
        });

        $(".enable-key").click(function(){
            $("#enable-key-id").val($(this).data("id"));
        });
    </script>
</body>

</html>