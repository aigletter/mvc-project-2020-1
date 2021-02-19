<?php


namespace App\Controllers;


class PaymentController
{
    public function isLogin()
    {
        session_start();
        return isset($_SESSION['auth']);
    }

    public function pay()
    {
        if ($this->isLogin()) {
            if (isset($_GET['csrf-token'])) {
                $amount = $_GET['amount'];
                echo 'Send money ' . $amount . ' to ' . $_GET['to'];
            } else {
                echo 'Are you hacker?';
            }

        } else {
            echo 'Not login';
        }
    }
}