<?php

class  PluginPhpsamlAuth extends Auth
{

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @param string $userName
     * @return $this
     */
    public function loadUserData($userName)
    {
        if($this->user->getFromDBbyName(addslashes($userName)) != ''){
			return $this;
		} elseif($this->user->getFromDBbyEmail(addslashes($userName)) != ''){
			return $this;
		}
    }

    /**
     * @return bool
     */
    public function checkUserData()
    {
        $this->auth_succeded = (bool)$this->user->fields;
        return $this->auth_succeded;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
