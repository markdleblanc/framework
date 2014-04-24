<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\storage\driver;

use framework\core\Response,
	framework\core\Resource,
	framework\core\exceptions\InvalidQueryException,
	framework\core\exceptions\InvalidStorageDriverException,
	\PDO,
	\RuntimeException,
	\PDOException;

/**
 *
 */
class SqlStorageDriver {
	/**
	 * Connection handle to the underlying database.
	 */
	private $handle;

	/**
	 * Contains any error response.
	 */
	private $error_response;

	/**
	 *
	 */
	public function __construct(array $properties) {
		// @note I'm aware that isset tests whether the value is equal to NULL. It is much faster, and none of these values
		//			should be null.
		if(!isset($properties['username'], $properties['password'], $properties['host'], $properties['database']))
		{
			throw new RuntimeException("No login credentials, or connection parameters.");
		}

		$host	  = $properties['host'];
		$database = $properties['database'];
		$username = $properties['username'];
		$password = $properties['password'];

		try {
			$this->handle = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		} catch (PDOException $exception) {
			throw new InvalidStorageDriverException("Unable to establish a connection with the persistence layer. " . $exception->getMessage());
		}
	}

	/**
	 * Returns record(s) from the database.
	 * @param array $parameters The parameters to retreive values for.
	 * @param string $table The name of the table to retreive records from.
	 * @param array $criteria The criteria that must be met before returning a record.
	 * @throws InvalidQueryException If an error occurs while executing the statement.
	 */
	public function read($parameters, $table, $criteria, $offset, $limit) {
		$query = "SELECT " . implode(", ", $parameters) . " FROM $table";
		if(count($criteria) != 0) {
			$keyset = array_keys($criteria);
			$values = array_values($criteria);

			foreach ($keyset as $key) {
				reset($keyset);
				if($key !== current($keyset)) {
					$query .= " AND $key = ?";
					continue;
				}
				$query .= " WHERE $key = ?";
			}
		}

		if($limit != 0)
			$query .= "LIMIT $offset, $limit";
		// echo $query . "<br/>";
		$statement = $this->handle->prepare($query);
		if(!$statement->execute(isset($values) ? $values : null)) {
			$errorInfo = $statement->errorInfo();
			throw new InvalidQueryException($errorInfo[2]);
		}

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Updates any record matching the criteria.
	 * @param array $parameters The key:value pairs to update.
	 * @param string $table The name of the table in which to update records.
	 * @param array $criteria the criteria that must be met before a record can be updated.
	 * @throws InvalidQueryException If an error occurs while executing the statement.
	 */
	public function update($parameters, $table, $criteria) {
		$query = "UPDATE $table SET";
		$keys = array_keys($parameters);
		$values = array_values($parameters);
		for($caret = 0; $caret < count($parameters); $caret++) {
			$key = $keys[$caret];
			if($caret == count($parameters) - 1) {
				$query .= " $key = ?";
				continue;
			} else $query .= " $key = ?,";
		}

		if(count($criteria) != 0) {
			$keyset = array_keys($criteria);
			$values = array_merge($values, array_values($criteria));

			foreach ($keyset as $key) {
				reset($keyset);
				if($key !== current($keyset)) {
					$query .= " AND " . \framework\seperateCamelCase($key) . " = ?";
					continue;
				}
				$query .= " WHERE " . \framework\seperateCamelCase($key) . " = ?";
			}
		}

		$statement = $this->handle->prepare($query);
		if(!$statement->execute(isset($values) ? $values : null)) {
			$errorInfo = $statement->errorInfo();
			throw new InvalidQueryException($errorInfo[2]);
		}
	}

	/**
	 * Inserts a record into the table.
	 * @param array $parameters The key:value pairs to insert.
	 * @param string $table The name of the table to insert a record into.
	 * @throws InvalidQueryException If an error occurs while executing the statement.
	 */
	public function create($parameters, $table) {
		$query = "INSERT INTO $table";
		$keys = array_keys($parameters);
		$values = array_values($parameters);

		$query .= ' (' . implode(", ", $keys) . ') VALUES (';
		for($caret = count($values) - 1; $caret >= 0; $caret--) {
			if($caret !== 0) $query .= "?, ";
			else $query .= "?);";
		}

		$statement = $this->handle->prepare($query);
		if(!$statement->execute($values)) {
			$errorInfo = $statement->errorInfo();
			throw new InvalidQueryException($errorInfo[0]);
		}
		
		$query = "SELECT LAST_INSERT_ID() as uid";
		$statement = $this->handle->prepare($query);
		if(!$statement->execute()) {
			$errorInfo = $statement->errorInfo();
			throw new InvalidQueryException($errorInfo[0]);
		}

		$columns = array_shift($statement->fetchAll(PDO::FETCH_ASSOC));
		return (int) $columns['uid'];
	}

	/**
	 * Removes all records that meet the criteria from the specific table.
	 * @param string $table The table to remove records from.
	 * @param array $criteria The criteria that must be met prior to deleting a record.
	 * @throws InvalidQueryException If an error occurs while executing the statement.
	 */
	public function delete($table, $criteria) {
		$query = "DELETE FROM $table";
		if(count($criteria) != 0) {
			$keyset = array_keys($criteria);
			$values = array_values($criteria);

			foreach ($keyset as $key) {
				reset($keyset);
				if($key !== current($keyset)) {
					$query .= " AND $key = ?";
					continue;
				}
				$query .= " WHERE $key = ?";
			}
		}
		$statement = $this->handle->prepare($query);
		if(!$statement->execute(isset($values) ? $values : null)) {
			$errorInfo = $statement->errorInfo();
			throw new InvalidQueryException($errorInfo[2]);
		}
	}
}