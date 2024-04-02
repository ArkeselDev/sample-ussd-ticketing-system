<?php

namespace Classes;

class Session
{

    protected $sessionsAlias = 'sessions';
    public $sessionData;
    private $dbmanager;

    public function __construct(protected string  $arkeselSessionID, protected string $phoneNumber, DBConnection $dbmanager)
    {
        $this->dbmanager = $dbmanager;
    }
    
    /**
     * Get current session
     */
    private function getSession() :array
    {
        $results = $this->dbmanager->runSelectQuery($this->sessionsAlias,[
            'session_id' => $this->arkeselSessionID,
            'number' => $this->phoneNumber
        ]);  

        if (count($results) > 0)
            $this->sessionData = $results[0];
        
        return $results;
    }
    
    /**
     * Create a new session
     */
    public function createSession(int $level, int $stage, $data = []) :bool
    {
        $created = $this->dbmanager->runInsertQuery($this->sessionsAlias, [
            'session_id' => $this->arkeselSessionID,
            'number' => $this->phoneNumber,
            'data' => json_encode($data),
            'level' => $level,
            'stage' => $stage,
        ]);

        $this->getSession();

        return $created;
    }

    /**
     * Update a new session
     */
    public function updateSession(int $level, int $stage, $data = []) :bool
    {
        $existingData = json_decode($this->sessionData['data'],true);
        $data = array_merge($existingData,$data);

        $updated = $this->dbmanager->runUpdateQuery($this->sessionsAlias, [
            'data' => json_encode($data),
            'level' => $level,
            'stage' => $stage,
        ],[
            'session_id' => $this->arkeselSessionID,
            'number' => $this->phoneNumber,
        ]);        
        return $updated;
    }
    
    /**
     * Check if the session exists
     */
    public function sessionExists()
    {
        $res = $this->getSession();      
        return count($res) > 0;
    }
    
    /**
     * Return the current level of the session
     */
    public function getCurrentLevel() :int|null
    {
        if (!$this->sessionData)
            $this->getSession();
        
        if (!$this->sessionData)
            return 0;

        return $this->sessionData['level'];
        // return $this->sessionData[0]['level'];
    }
    
    /**
     * resolve the next stage that has to be rendered 
     * 
     */
    public function getNextStage()
    {
        if (!$this->sessionData)
            $this->getSession();
        
        if (!$this->sessionData)
            return 0;

        return $this->sessionData['stage'];
        // return $this->sessionData[0]['stage'];
    }
    

    /***
     * Return intended quit ussd session
     */
    public function quitSession($sessionId,$userId,$msisdn)
    {
        echo 'An error occured!';
        echo http_response_code(502);
        header('Content-Type: application/json');
        return [
            'sessionID' => $sessionId,
            'userID' => $userId,
            'msisdn' => $msisdn,
            'message' => 'Failed to start. Please try again',
            'continueSession' => false
        ];
    }
}