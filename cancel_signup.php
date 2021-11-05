<?php
require "config.php";
require "functions/conn.php";
require "functions/functions_user.php";

$confirmation_code = !empty($_REQUEST['confirmation_code']) ? $_REQUEST['confirmation_code'] : NULL;
$submit            = !empty($_REQUEST['submit'])            ? $_REQUEST['submit']            : NULL;
$email             = !empty($_REQUEST['email'])             ? $_REQUEST['email']             : NULL;

$check_confirmation_code = false;
$invalid_email = false;
$error = 0;

if(!empty($confirmation_code))
{
    $ret = checkConfirmationCode($confirmation_code);
    if($ret > 0)
        $check_confirmation_code = true;
}

if($check_confirmation_code && $submit == "submit" && !empty($email))
{
    $ret = readUser($email, NULL, $confirmation_code);

    if(count($ret) > 0)
    {
        $ret = deleteUser($confirmation_code);
        $error = $ret ? 1 : 2;
    }
    else
        $invalid_email = true;
}
else if(empty($email) && $submit == "submit")
    $invalid_email = true;

?>

<!DOCTYPE html>
<html style="height: 100%; width: 100%" lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <style>
        .button
        {
            border-radius: 1.09rem;
            font-size: 20px;
        }

        .desc
        {
            font-size: 13px;
        }

        .small
        {
            font-size: 10px;
        }
    </style>
</head>

<body style="height: 100%; width: 100%">
    <table style="height: 100%; width: 100%">
        <tbody>
            <tr>
                <td class="align-middle">
                    <div class="row d-flex justify-content-center m-0" style="width: 100% !important;">
                        <div class="card p-4 border-0" style="width: 30rem">
                            <div class="card-body">
                                <?php
                                if($check_confirmation_code && $error == 0)
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Remover Conta</h3>

                                    <form method="post">
                                        <input type="hidden" name="confirmation_code" value="<?php echo !empty($_REQUEST['confirmation_code']) ? $_REQUEST['confirmation_code'] : "" ?>">
                                        <center>
                                            <input type="email" name="email" class="form-control" placeholder="Insira o email cadastrado para remover a conta">

                                            <?php
                                            if($invalid_email)
                                            {
                                            ?>
                                                <p class="mt-1 small text-danger" style="text-align: left">Email incorreto!</p>
                                            <?php
                                            }
                                            ?>

                                            <button type="submit" name="submit" value="submit" class="btn btn-danger button px-4 mt-3">Remover conta</button>
                                        </center>
                                    </form>

                                    <p class="text-center mt-5 small">
                                        Se deseja ativar essa conta, clique 
                                            <a href="confirm_signup.php?confirmation_code=<?php echo !empty($_REQUEST['confirmation_code']) ? $_REQUEST['confirmation_code'] : "" ?>">aqui</a>
                                        confirmar o cadastro
                                    </p>
                                <?php
                                }
                                else if(!$check_confirmation_code)
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Código de confirmação inválido!</h3>
                                <?php
                                }
                                else if($error == 1)
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Conta removida com sucesso<br><br>Pedimos desculpas pelo ocorrido</h3>
                                <?php
                                }
                                else
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Erro ao remover conta, tente novamente mais tarde!</h3>
                                <?php
                                }
                                ?>
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