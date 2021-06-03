<?php
#
#	General
#
	function service_end($status, $data)
	{
		if( !is_int( $status ) )
			throw new Exception("El estado debe ser un numero entero", 1);
		
		header('Content-Type: application/json; charset=utf-8', true);
		die ( json_encode( [ 'status' => $status, 'data' => $data ] ) );
	}
	
	function service_get_ip($full = false) {
		
		$ipaddress = '';
		if($full)
		{
			$ipaddress = @$_SERVER['HTTP_CLIENT_IP'] . ';' .
            $ipaddress .= @$_SERVER['HTTP_X_FORWARDED_FOR'] . ';' .
			$ipaddress .= @$_SERVER['REMOTE_ADDR'] . ';';
			$ipaddress .= @$_SERVER['HTTP_X_FORWARDED'] . ';';
			$ipaddress .= @$_SERVER['HTTP_FORWARDED_FOR'] . ';';
			$ipaddress .= @$_SERVER['HTTP_FORWARDED'];
		}
		else 
		{
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			else if (isset($_SERVER['HTTP_CLIENT_IP']))
				$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			else if(isset($_SERVER['HTTP_X_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			else if(isset($_SERVER['HTTP_FORWARDED']))
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			else if(isset($_SERVER['REMOTE_ADDR']))
				$ipaddress = $_SERVER['REMOTE_ADDR'];
		}
		return $ipaddress;
	}

	function get_data() {

		$json = @$_POST['json_content'];
		if(!empty($json)) {
			$data = json_decode($json, true);
			return $data;
		}

		return false;
	}

	function register_action() {
		//	Sacamos el json
		$data = get_data();

		if ($data != false) {

			//	Borramos las acciones del mismo tipo...
			$sql = 'DELETE FROM action_box WHERE token = :token AND action_id = :action_id';
			service_db_excecute(
				$sql, 
				[ 
					'token' => $data['token'], 
					'action_id' => $data['action_id'] 
				]
			);

			// Insertamos la accion...
			$sql = 'INSERT INTO action_box(token, action_id, action_data, created_time) ' .
				'VALUES (:token, :action_id, :action_data, :created_time)';

			service_db_excecute(
				$sql, 
				[ 
					'token' => $data['token'],
					'action_id' => $data['action_id'],
					'action_data' => !empty(@$data['action_data']) ? @$data['action_data'] : '',
					'created_time' => time()
				]
			);

			service_end(Status::Success, '');
			
		}

		service_end(Status::Error, 'La petición no traia datos para registrar la acción.');
	}

	function register_info() {
		/*
			required object:
				data [
					token: string(8),
					object_data: string(max)
				]
		*/
		$data = get_data();

		if ($data != false && !empty($data['object_data'])) {

			//	Borramos la info anterior del mismo tipo...
			$sql = 'DELETE FROM spreadbox WHERE token = :token';
			service_db_excecute(
				$sql, 
				[ 'token' => $data['token'] ]
			);

			// Insertamos la accion...
			$sql = 'INSERT INTO spreadbox(token, object_data, created_time) ' .
				'VALUES (:token, :object_data, :created_time)';

			service_db_excecute(
				$sql, 
				[ 
					'token' => $data['token'],
					'object_data' => $data['object_data'],
					'created_time' => time()
				]
			);

			service_end(Status::Success, '');
			
		}

		service_end(Status::Error, 'La petición no traia datos para registrar.');
	}

	function get_actions() {
		/*
			required object:
				data [
					token: string(8),
					created_time: int(10)
				]
		*/

		$data = get_data();
		$result = [];

		if ($data != false) {
			$result = service_db_select(
				'SELECT * FROM action_box WHERE token = :token AND created_time > :created_time',
				[
					'token' => $data['token'],
					'created_time' => $data['created_time']
				]
			);

		}

		service_end(Status::Success, $result);
	}

	function get_info() {
		$data = get_data();
		$result = null;

		if ($data != false) {
			$result = service_db_select(
				'SELECT * FROM spreadbox WHERE token = :token AND created_time > :created_time',
				[
					'token' => $data['token'],
					'created_time' => $data['created_time']
				]
			);

			$result = empty($result) ? null : $result[0];
		}

		service_end(Status::Success, $result);
	}
