<?php
require "config.php";
require "functions/conn.php";
require "functions/functions_user.php";

$reset_password_code = !empty($_REQUEST['reset_password_code']) ? $_REQUEST['reset_password_code'] : NULL;
$submit              = !empty($_REQUEST['submit'])              ? $_REQUEST['submit']              : NULL;
$password            = !empty($_REQUEST['password'])            ? $_REQUEST['password']            : NULL;
$confirm_password    = !empty($_REQUEST['confirm_password'])    ? $_REQUEST['confirm_password']    : NULL;

$invalid_password = false;
$mismatch_password = false;
$error = 0;

if(!empty($reset_password_code))
{
    $ret = checkResetPasswordCode($reset_password_code);
    if(!$ret > 0)
        $error = 1;
}
else
    $error = 1;

if($error == 0 && $submit == "submit" && !empty($password))
{
    if($password == $confirm_password)
    {
        $ret = resetPassword($reset_password_code, md5($password));

        if(!$ret)
            $error = 2;
    }
    else
        $mismatch_password = true;
}
else if($error == 0 && $submit == "submit" && empty($password))
    $invalid_password = true;

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
            background-color: #FF6884;
            border-radius: 1.09rem;
            font-size: 20px;
            color: #FFFFFF;
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
                                if($error == 0 && empty($submit) || $invalid_password || $mismatch_password)
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Redefinir senha</h3>

                                    <form method="post">
                                        <input type="hidden" name="reset_password_code" value="<?php echo $reset_password_code ?>">
                                        <center>
                                            <input type="password" name="password" class="form-control" placeholder="Insira sua nova senha">

                                            <?php
                                            if($invalid_password)
                                            {
                                            ?>
                                                <p class="mt-1 small text-danger" style="text-align: left">Senha inválida!</p>
                                            <?php
                                            }
                                            ?>

                                            <input type="password" name="confirm_password" class="form-control mt-2" placeholder="Confirme sua senha">

                                            <?php
                                            if($mismatch_password)
                                            {
                                            ?>
                                                <p class="mt-1 small text-danger" style="text-align: left">As senhas não conferem!</p>
                                            <?php
                                            }
                                            ?>

                                            <button type="submit" name="submit" value="submit" class="btn button px-4 mt-3">Redefinir senha</button>
                                        </center>
                                    </form>

                                    <p class="text-center mt-5 small">
                                        Se você não solicitou a redefinição, não altere a senha
                                    </p>
                                <?php
                                }
                                else if($error == 0 && $submit == "submit")
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Senha redefinida com sucesso!</h3>
                                <?php
                                }
                                else if($error == 1)
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Código de redefinição inválido!</h3>
                                <?php
                                }
                                else
                                {
                                ?>
                                    <h3 class="card-title p-2 text-center">Erro ao redefinir senha, tente novamente mais tarde!</h3>
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