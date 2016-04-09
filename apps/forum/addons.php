<?php
namespace apps\forum;
use \Mega\cls\core as core;
use \Mega\cls\network as network;
use \Mega\cls\Database as db;


trait addons {

    /*
     * return number of all replays
     * @return integer
     */
    public function replaysCount(){
        $orm = db\orm::singleton();
        return $orm->count('forums_replays');
    }

    /*
     * return number of all topics
     * @return integer
     */
    public function topicsCount(){
        $orm = db\orm::singleton();
        return $orm->count('forums_topics');
    }
}
