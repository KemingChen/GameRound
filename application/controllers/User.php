<?php

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("UserModel");
    }

    //創建使用者帳號密碼
    public function signup($name, $account, $password)
    {
        echo $this->UserModel->create($name, $account, $password);
    }

    //確認名字是否存在
    public function isNameExist($name)
    {
        echo $this->UserModel->exist("userName", $name);
    }

    //確認此帳號是否存在
    public function isAccountExist($account)
    {
        echo $this->UserModel->exist("account", $account);
    }

    //登入
    public function login($key, $gameId, $account, $password)
    {
        $this->load->model("GameModel");
        $gAuth = $this->GameModel->checkKey($gameId, $key);//確認此key可以用來登入此遊戲
        $auth = $this->UserModel->checkAuth($account, $password);
        //print_r($auth);
        if ($gAuth && $auth != false)
        {
            $this->GameModel->deleteKey($key);//刪除登入時使用的Key
            $key = $this->UserModel->keygen($auth["id"], $gameId, -1);//產生新的溝通key
            $this->UserModel->saveKey($auth["id"], $gameId, $key);//儲存溝通key 使下次可以做認證
            echo $key;
        }
        else
        {
            echo 0;
        }
    }
    
    public function logout($key)
    {
        $this->UserModel->deleteKey($key);
    }
}

?>