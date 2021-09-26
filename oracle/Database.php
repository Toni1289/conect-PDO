<?php

/**
 * @version 1.0.0
 * @author AA20567
 */

class DBTables
{
	
	const TABLE_HOTHOUSE	= 'HOT_HOTHOUSE';
	const TABLE_LAST_UPDATE = 'HOT_LAST_UPDATE';
	const TABLE_BOM_SAP	 	= 'TBL_GT_BOM';
	const TABLE_ESCALA	 	= 'TBL_GT_ESCALA';
	
}


/**
 * 	Classe para gerenciamento do banco de dados
 */

class Database
{
	
	private static $PATTERNS = '/([0-9]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{2,4})/';
	
	protected	$host      	= '',							// Host da base de dados
				$db_service	= '',   						// Serviço do banco de dados
				$db_port   	= 0,   							// Porta de Serviço do banco de dados
				$password  	= '',          					// Senha do usuário da base de dados
				$user      	= '',   						// Usuário da base de dados
				$charset   	= '',      						// Charset da base de dados
				$conn_data  = '',							// Parâmetro de conexão 
				$pdo       	= null, 						// Nossa conexão com o BD
				$error     	= null, 						// Configura o erro
				$debug     	= false; 						// Mostra todos os erros
    public	    $last_id   	= null;      					// Último ID inserido
	
	/**
	 * 	Construtor da classe
	 *
	 * @param string $host
	 * @param string $db_service
	 * @param string $db_port
	 * @param string $password
	 * @param string $user
	 * @param string $charset
	 * @param boolean $debug
	 */
	public function __construct($host = null, $db_service = null, $db_port = null, $password = null, $user = null,
			$charset  = null, $debug    = null, $connect_data = null )
	{

		$this->host		  = ($host 		 == null ? HOSTNAME 	: $host 		);
		$this->db_service = ($db_service == null ? DB_SERVICE 	: $db_service 	);
		$this->db_port    = ($db_port 	 == null ? DB_PORT 		: $db_port		);
		$this->password   = ($password 	 == null ? DB_PASSWORD	: $password		);
		$this->user       = ($user 		 == null ? DB_USER		: $user			);
		$this->charset    = ($charset 	 == null ? DB_CHARSET	: $charset		);
		$this->debug      = ($debug 	 == null ? DEBUG		: $debug		);
		
		
		$this->conn_data = ( $connect_data == null ? 'SERVICE_NAME' : $connect_data);
		
		$this->connect();

	}
	
	/**
	 * 	Cria a conexão PDO
	 */
	final protected function connect() {

		$pdo_details  = "oci:dbname=
								(DESCRIPTION =
									(ADDRESS_LIST =
										(ADDRESS = (PROTOCOL = TCP)(HOST = {$this->host})(PORT = {$this->db_port}))
									)
									(CONNECT_DATA =
										({$this->conn_data} = {$this->db_service})
									)
								)";
		$pdo_details .= ";charset={$this->charset}"
					 .  ";alter_session=ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/YYYY HH24:MI:SS'";
			
		try {

			$this->pdo = new PDO($pdo_details, $this->user, $this->password);

			if ( $this->debug === true ) {
				$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}

			unset( $this->host     	 );
			unset( $this->db_service );
			unset( $this->db_port 	 );
			unset( $this->password 	 );
			unset( $this->user     	 );
			unset( $this->charset    );

		} catch (PDOException $e) {

			if ( $this->debug === true ) {
				echo "Erro: " . $e->getMessage();

			}

			die();
		}
	}

	/**
	 * 	Query
	 * @param string $stmt
	 * @param array $data_array
	 */
	public function query( $stmt, $data_array = null, $id = null ) {

        $query = $this->pdo->prepare( $stmt );

        if ( $id != null ) {
            $query->bindParam($id, $this->last_id, PDO::PARAM_INT, 11);
        }

        $check_exec = $query->execute( $data_array );

        if ( $check_exec ) {
			return $query;
		} else {
			$error       = $query->errorInfo();
			$this->error = $error[2];
			return false;
		}
	}

	/**
	 * 	Insere os valores e retorna o �ltimo id
	 *
	 * @param string $table
	 */
	public function insert( $table ) {

		$cols = array();
		$place_holders = '(';

		$values = array();

		$j = 1;

		$data = func_get_args();

		if ( ! isset( $data[1] ) || ! is_array( $data[1] ) ) {
			return;
		}

		for ( $i = 1; $i < count( $data ); $i++ ) {

			foreach ( $data[$i] as $col => $val ) {
					
				if ( $i === 1 ) {
					$cols[] = "$col";
				}

				if ( $j <> $i ) {
					$place_holders .= '), (';
				}

				if ( mb_strlen( $val ) == 19 ) {
					if ( preg_match( Database::$PATTERNS, $val) ) {
						$place_holders .= "TO_DATE(?, 'DD/MM/YYYY HH24:MI:SS'), ";
					} else {
						$place_holders .= '?, ';
					}
				} else {
					if ( mb_strlen( $val ) == 10 ) {
						if ( preg_match( Database::$PATTERNS, $val) ) {
							$place_holders .= "TO_DATE(?, 'DD/MM/YYYY'), ";
						} else {
							$place_holders .= '?, ';
						}
					} else {
						$place_holders .= '?, ';
					}
				}

				$values[] = $val;
				
				$j = $i;
			}

			$place_holders = substr( $place_holders, 0, strlen( $place_holders ) - 2 );
		}

		$cols = implode(', ', $cols);

		$stmt = "INSERT INTO $table ( $cols ) VALUES $place_holders)";

		$insert = $this->query( $stmt, $values );

/*
		if ( $insert ) {

			if ( method_exists( $this->pdo, 'lastInsertId' ) && $this->pdo->lastInsertId() 	) {
				$this->last_id = $this->pdo->lastInsertId();
			}
			return $this->last_id;
		}
*/
		
		return $insert;
	}


	/**
	 * 	Atualiza um linha no banco de dados
	 *
	 * @param string $table
	 * @param string $where_field
	 * @param string $where_field_value
	 * @param string $values
	 */
	public function update( $table, $where_field, $where_field_value, $values ) {

		if ( empty($table) || empty($where_field) || empty($where_field_value)  ) {
			return;
		}

		$stmt = " UPDATE $table SET ";

		$set = array();

		$where = " WHERE $where_field = ? ";

		if ( ! is_array( $values ) ) {
			return;
		}

        foreach ( $values as $column => $value ) {
            if ( mb_strlen( $value ) == 19 ) {
                if ( preg_match( Database::$PATTERNS, $value) ) {
                    $set[] = " $column = TO_DATE( ? , 'DD/MM/YYYY HH24:MI:SS')";
                } else {
                    $set[] = " $column = ? ";
                }
            } else {
                if ( mb_strlen( $value ) == 10 ) {
                    if ( preg_match( Database::$PATTERNS, $value) ) {
                        $set[] = " $column = TO_DATE( ? , 'DD/MM/YYYY')";
                    } else {
                        $set[] = " $column = ? ";
                    }
                } else {
                    $set[] = " $column = ? ";
                }
            }
        }

		$set = implode(', ', $set);

		$stmt .= $set . $where;

		$values[] = $where_field_value;

		$values = array_values($values);

		$update = $this->query( $stmt, $values );

		if ( $update ) {
			return $update;
		}

		return;
	}

	/**
	 * Deleta um registro
	 *
	 * @param string $table
	 * @param string $where_field
	 * @param string $where_field_value
	 */
	public function delete( $table, $where_field, $where_field_value ) {

		if ( empty($table) || empty($where_field) || empty($where_field_value)  ) {
			return;
		}

		$stmt = " DELETE FROM $table ";

		$where = " WHERE $where_field = ? ";

		$stmt .= $where;

		$values = array( $where_field_value );

		$delete = $this->query( $stmt, $values );

		if ( $delete ) {
			return $delete;
		}

		return;
	}
}