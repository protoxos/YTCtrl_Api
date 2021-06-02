<?php
	/**
	*	Realiza una consulta a la base de datos
	*
	* 	@global $service_db
	*
	* 	@param $query Es la consulta que se ha de realizar.
	*	@param $params
	*/
	$_service_db_error = false;
	function service_db_select( $query, $params = [] )
	{
		global $service_db, $_service_db_error;

		try
		{
			$q = $service_db->prepare($query);
			$q->execute($params);
		}
		catch(Exception $e) {
			$_service_db_error = $e;
			return false;
		}
		return 	$q->fetchAll( PDO::FETCH_ASSOC );
	}
	/**
	 * Ejecuta una consulta modificadora a en la base de datos actual. Si se ejecuta la consulta con $query = array en lugar de true o false, devuelve el numero de registros modificados correctamente
	 * @param mixed $query En formato string se considera como consulta, en array ya no se toma en cuenta $params y debera ser [ $querystring, $params ] cada elemento
	 * @param array $params Opcional: son los parametros que se curarán con el prepare
	 */
	function service_db_excecute( $query, $params = [] )
	{
		global $service_db, $_service_db_error;

		if( is_array( $query ) )
		{
			$total = 0;

			foreach($query as $val)
			{
				try
				{
					$q = $service_db -> prepare( $val[0] );
					if( $q -> execute( $val[1] ?: [] ))
						$total++;
				}
				catch( Exception $e )
				{
					$_service_db_error = $e;
				}
			}

			return $total;
		}
		else
		{
			try
			{
				$q = $service_db -> prepare( $query );
				return $q -> execute( $params );
			}
			catch( Exception $e ){
				$_service_db_error = $e;
				return false;
			}
		}
	}
	function service_db_error()
	{
		global $_service_db_error;
		return $_service_db_error === false ? [0,0,'(desconocido)'] : $_service_db_error->errorInfo;
	}

	//	Cargamos la conexión a la base de datos con los datos de moodle
	try 
	{ 
		$service_db = new PDO(
			"mysql:host=localhost;dbname=" . DB_NAME, 
			
			DB_USER,

			DB_PASS, 
			
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_EMULATE_PREPARES => false,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
			]); 
	}
	catch (Exception $x) 
	{ 
		service_end(Status::Error, "No se ha podido conectar a la base de datos."); 
	}
