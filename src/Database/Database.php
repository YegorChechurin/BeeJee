<?php

namespace App\Database;

// https://github.com/YegorChechurin/Social-Nano-Network/blob/database/skeleton/Database/Database.php
class Database
{
	private const DSN = 'mysql:dbname=beejee;host=localhost';

    private const USER = 'root';

    private const PASSWORD = '';

    private $conn;

    /**
     * Creates PDO database connection
     */
	public function __construct() {
        $this->conn = new \PDO(self:: DSN, self::USER, self::PASSWORD);
	}

    /**
     * Performs INSERT sql operation by means of PDO prepared statement
     *
     * @param string $table - Name of database table where data is inserted in.
     * @param string[] $fields - Name of table fields where data is inserted in.
     * @param mixed[] $values - Data to be inserted. Each data value 
     * corresponds to a specific table field, thus data values should be 
     * stated in the order which matches the order table fields are stated in.
     */
    public function insert(string $table, array $fields, array $values): void
    {
        $format = 'INSERT INTO %s (%s) VALUES (:%s)';

        $names = implode(',',$fields);
        $params = implode(',:',$fields);
        $query = sprintf($format, $table, $names, $params);

        $prep = $this->conn->prepare($query);

        $n = count($fields);
        for ($i=0; $i < $n; $i++) { 
            $prep->bindParam(':'.$fields[$i], $values[$i]);
        }

        $prep->execute();
    }

    /**
     * Performs SELECT sql operation 
     *
     * By default performs a simple SELECT operation without a WHERE clause.
     * WHERE clause should be passed as a parameter and only in a form of 
     * prepared statement. 
     *
     * @param string $table - Name of database table data is selected from.
     * @param string $className - Nmae of class selected data is associated
     * with.
     * @param string $clause_exp - WHERE clause in a form of prepared 
     * statement. It does NOT contain the WHERE keyword. Example:
     * $clause_exp = 'field=:parameter';
     * @param mixed[] $clause_pars - Associative array, where keys are
     * named parameter placeholders and values are parameters from the 
     * prepared statement of WHERE clause. Illustration of how this array 
     * should look for a given WHERE clause: 
     * $clause_exp = 'field1=:param1 AND field2=:param2'
     * $clause_pars = [':param1'=>value_of_param1,':param2'=>value_of_param2]
     *
     * @return Object[] - Array of objects. Each object
     * corresponds to a row in the database table.  
     */
    public function select(string $table, string $className, 
    	string $clause_exp = null, array $clause_pars = null): array 
    {
        if ($clause_exp==null) {
            $format = "SELECT * FROM %s";

            $query = sprintf($format, $table);
            $prep = $this->conn->query($query);
        } else {
            $format = "SELECT * FROM %s WHERE {$clause_exp}";

            $query = sprintf($format, $table);
            $prep = $this->conn->prepare($query);

            if ($clause_pars) {
                foreach ($clause_pars as $key => $value) {
                   $prep->bindValue($key, $value);
                }
            }

            $prep->execute();
        }

        $result = [];
        while ($obj = $prep->fetchObject($className)) {
        	$result[] = $obj;
        }

        return $result;
    }

    public function selectWithLimit(string $table, string $className, int $offset, int $numOfRecords): array
    {
    	$format = "SELECT * FROM %s LIMIT :offset, :numOfRecords";

        $query = sprintf($format, $table);
        $prep = $this->conn->prepare($query);

        $prep->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $prep->bindValue(':numOfRecords', $numOfRecords, \PDO::PARAM_INT);

        $prep->execute();

        $result = [];
        while ($obj = $prep->fetchObject($className)) {
        	$result[] = $obj;
        }

        return $result;
    }

    public function selectTotalAmountOfRecords(string $table): int
    {
    	$format = "SELECT COUNT(*) FROM %s";
    	$query = sprintf($format, $table);

    	$result = $this->conn->query($query)->fetch(\PDO::FETCH_ASSOC);

    	return (int)$result['COUNT(*)'];
    }
    
    /**
     * Performs UPDATE sql operation by means of PDO prepared statement
     *
     * @param string $table - Name of database table where data is updated.
     * @param string[] $fields - Name of table fields where data is updated.
     * @param string $clause - WHERE clause in a form of prepared 
     * statement. It does NOT contain the WHERE keyword. Example:
     * $clause = 'field=:parameter';
     * @param mixed[] $parameter_map - Associative array, where keys are
     * named parameter placeholders and values are parameters from the 
     * query to be performed. Illustration of how this array should look 
     * for a given set of fields and WHERE clause: 
     * $fields = ['field1',field2];
     * $clause = 'field3=:par3 AND field4=:par4';
     * $parameter_map = [
     *     ':field1'=>value_to_be_written_into_field1, 
     *     ':field2'=>value_to_be_written_into_field2,
     *     ':par3'=>value_of_par3,
     *     ':par4'=>value_of_par4
     *];
     */
    public function update(string $table, array $fields, string $clause, 
    	array $parameter_map): void 
    {
        $format = "UPDATE %s SET %s WHERE {$clause}";

        $param_fields = [];
        foreach ($fields as $field) {
            $param_fields[] = $field.'=:'.$field;
        }

        $structure = implode(',',$param_fields);
        $query = sprintf($format,$table,$structure);

        $prep = $this->conn->prepare($query);

        foreach ($parameter_map as $key => $value) {
            $prep->bindValue($key, $value);
        }

        $result = $prep->execute();
    }
}