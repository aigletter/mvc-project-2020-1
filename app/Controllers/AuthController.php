<?php


namespace App\Controllers;


use Aigletter\Core\Application;
use Aigletter\Core\Components\Database\Db;

class AuthController
{
    protected $salt = '4aBc;d(12@34';

    public function form()
    {
        ?>
            <div>
                <form method="post" action="/auth/login">
                    <input name="login">
                    <br><br>
                    <input type="password" name="password">
                    <br><br>
                    <button type="submit">Login</button>
                </form>
            </div>
        <?php
    }

    public function login()
    {
        /** @var Db $db */
       $db = Application::getInstance()->get(Db::class);
       $user = $db->getQueryBuilder()->table('users')->where([
           'login' => $_POST['login'],
           'password' => md5($_POST['password'] . $this->salt),
       ])->one();

       //$sql = "SELECT * FROM user WHERE login = '1234'";

       if ($user) {
           session_start();
           $_SESSION['auth'] = true;
           $_SESSION['id'] = $user['id'];
           $_SESSION['secret'] = md5($user['id']);
           header('Location: /');
       }

        header('Location: /auth/form');
    }

    public function view()
    {
        session_start();
        /** @var Db $db */
        $db = Application::getInstance()->get(Db::class);
        $pdo = $db->getConnection();

        $sql = "SELECT * FROM users WHERE id  = " . $_SESSION['id'];
        //$sql = "SELECT * FROM users WHERE id  = " . (int) $_GET['id'];
        $statement = $pdo->query($sql);
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        /*$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $statement->execute([":id" => $_GET['id']]);
        $user = $statement->fetchAll(\PDO::FETCH_ASSOC);*/

        echo '<p>' . $user['name'] . '</p>';
        echo '<p>' . $user['description'] . '</p>';
        echo '<p><a href="http://mvc-project.loc/payment/send?amount=1000&to=1231&csrf-token='
            . $_SESSION['csrf-token'] . '">Send money</a></p>';

    }

    public function update()
    {
        ?>
            <form method="post" action="/auth/store">
                <input name="password">
                <br><br>
                <textarea name="description"></textarea>
                <br><br>
                <button type="submit">Send</button>
            </form>
        <?php
    }

    public function store()
    {
        session_start();
        /** @var Db $db */
        $db = Application::getInstance()->get(Db::class);

        $description = $_POST['description'];
        $description = htmlentities($description);
        $sql = "UPDATE users SET description = '" . $description . "'";

        if (isset($_POST['password'])) {
            $password = md5($_POST['password'] . $this->salt);
            $sql .= ", password = '" . $password . "'";
        }

        $sql .= " WHERE id = " . $_SESSION['id'];
        $db->query($sql);


        return header('Location: /auth/view');
    }
}