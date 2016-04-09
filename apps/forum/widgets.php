<?php
namespace apps\forum;
class widgets extends module{

    /*
     * show last replays in forum
     * @return array [title,content]
     */
    public function lastReplays(){
        return $this->moduleLastReplays();
    }

    /**
     * show last topic that created
     * @return array [title,content]
     */
    public function lastTopics(){
        return $this->moduleLastTopics();
    }
}
