<?php

class Room extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("OutputModel", "out");
        $this->load->model("RoomModel");
        $this->load->model("AuthModel");
    }

    // 創建房間
    public function create($title, $minPlayer, $maxPlayer, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        $this->checkPlayerNumber($minPlayer, $maxPlayer);

        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $roomId = $this->RoomModel->create($gameId, $title, $minPlayer, $maxPlayer);
        $this->out->save("roomId", $roomId);
        $this->out->show();
    }

    // 加入房間
    public function join($iRoomId, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $isPermit = $this->RoomModel->join($userId, $iRoomId, $this->out);
        $this->AuthModel->editCommuKey($nextCKey, $iRoomId, $this->out);
        $this->out->save("Join", $isPermit);
        $this->out->show();
    }
    
    // 離開房間
    public function leave($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->leave($userId, $roomId, $this->out);
        $this->AuthModel->editCommuKey($nextCKey, 0, $this->out);
        $this->out->save("Leave", true);
        $this->out->show();
    }
    
    // 查詢所有未開始房間
    public function getList($cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        $this->RoomModel->rooms($gameId, $this->out);
        $this->out->show();
    }
    
    public function modifyTitle($title, $cKey)
    {
        $nextCKey = $this->AuthModel->getNextCommuKey($cKey, $this->out);
        list($key, $userId, $gameId, $roomId) = explode('_', $cKey);
        
    }
    
    // 遊戲設定玩家人數上下限 防呆機制
    private function checkPlayerNumber($minPlayer, $maxPlayer)
    {
        if (!($minPlayer >= 2 && $minPlayer <= $maxPlayer && $maxPlayer <= 20))
        {
            $this->out->wrong("MaxPlayer = 2~20, MinPlayer <= 2 <=MaxPlayer");
        }
    }
}

?>