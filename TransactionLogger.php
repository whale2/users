<?php

  class TransactionLogger {
  
    public static function Log($account_id, $engine, $amount, $message) {
    
      $db = UserConfig::getDB();
      
      if (!($stmt = $db->prepare('INSERT INTO '.UserConfig::$mysql_prefix.
        'transaction_log (date_time, account_id, engine, amount, message) VALUES (?, ?, ?, ?, ?)')))
          throw new Exception("Can't prepare statement: ".$db->error);

      if (!$stmt->bind_param('sisds',date('Y-m-d H:i:s'),$account_id,$engine,$amount,$message))
        throw new Exception("Can't bind parameter".$stmt->error);
        
      if (!$stmt->execute())
        throw new Exception("Can't execute statement: ".$stmt->error);
        
      $id = $db->insert_id;
      $stmt->close();
      return $id;
    }
    
    public static function getAccountTransactions($account_id, $from = NULL, $to = NULL) {

      $db = UserConfig::getDB();
      
      $query = 'SELECT transaction_id, date_time, engine, amount, message FROM '.
        UserConfig::$mysql_prefix.'transaction_log WHERE account_id = ?'.
        (is_null($from) ? '' : ' AND date_time >= ?').
        (is_null($to)   ? '' : ' AND date_time <= ?');
        
      if (!($stmt = $db->prepare($query)))
        throw new Exception("Can't prepare statement: ".$db->error);
        
      if (!$stmt->bind_param('i'.(is_null($from) ? '' : 's').(is_null($to) ? '' : 's'), $account_id, $from, $to))
        throw new Exception("Can't bind parameter".$stmt->error);
        
      if (!$stmt->execute())
        throw new Exception("Can't execute statement: ".$stmt->error);
        
      if(!$stmt->bind_result($t_id, $date_time, $engine, $amount, $message))
        throw new Exception("Can't bind result: ".$stmt->error);
        
      $t = array();
      while($stmt->fetch() === TRUE)
        $t[] = array(
          'transaction_id' => $t_id,
          'date_time' => $date_time, 
          'account_id' => $account_id,
          'engine' => $engine,
          'amount' => $amount,
          'message' => $message);
      
      $stmt->close();
                    
      return $t;   
    }
  }