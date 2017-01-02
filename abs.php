<?php
abstract class lock_abs{

    protected $_key = null;

    protected $_id = null;

    protected $_fullKey = null;

    protected $_tries = 1;

    protected $_sleepForTry = 300;//100ms

    protected $_locked = false;

    protected $_lockExpire = 6;//6s

    /**
     *
     * @var Module_Player_Role
     */
    protected $_role = null;

    public function __construct($id, $key, $expire=null, $addToPool=true, $tries=false){
        $this->_id = $id;
        $this->_key = $key;
        $this->_fullKey = $this->_genFullKey($this->_id, $this->_key);
        if (isset(lock_proc::$exists[$this->_fullKey])) { //如果在同一个进程已经上锁过相同的锁，就不重复上了
            return lock_proc::$exists[$this->_fullKey];
        }

        if (is_int($tries)) $this->_tries = $tries;

        if($expire && $expire>0){
            $this->_lockExpire = $expire;
        }

        $this->_init();
        lock_proc::$exists[$this->_fullKey] = $this;
        if ($addToPool) {
            lock_pool::share()->add($this);
        }
    }

    protected function _init(){
    }

    abstract protected function _genFullKey($id, $key);

    abstract public function unlock();
}