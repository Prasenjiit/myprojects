<?php
//require_once(dirname(__FILE__) . '/class.db.php');
//require_once(dirname(__FILE__) . '/class.tree.php');
require_once(app_path('Mylibs'). '/class.db.php');
require_once(app_path('Mylibs'). '/class.tree.php');
$servername = config()->get('database.connections.mysql.host');
$username   = config()->get('database.connections.mysql.username');
$password   = config()->get('database.connections.mysql.password');
$dbname     = config()->get('database.connections.mysql.database');
if(isset($_GET['operation'])) {
	$fs = new tree(db1::get('mysqli://'.$username.'@'.$servername.'/'.$dbname), array('structure_table' => 'tree_struct', 'data_table' => 'tree_data', 'data' => array('nm')));
	try {
		$rslt = null;
		switch($_GET['operation']) {
			case 'analyze':
				var_dump($fs->analyze(true));
				die();
				break;
			case 'get_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$temp = $fs->get_children($node);
				$rslt = array();
				foreach($temp as $v) {
					$x=$v['id'];
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);
					$sql = "SELECT document_id FROM tbl_documents WHERE parent_id = '$x'";
					$result = $conn->query($sql);
					//temp documents
					$sql_temp = "SELECT document_id FROM tbl_temp_documents WHERE parent_id = '$x'";
					$result_temp = $conn->query($sql_temp);
					//checkout documents
					$sql_chk = "SELECT document_id FROM tbl_documents_checkout WHERE parent_id = '$x'";
					$result_chk = $conn->query($sql_chk);
					$sql_insert = "UPDATE `tree_data` SET `doc_count`='$result->num_rows',`temp_doc_count`='$result_temp->num_rows',`chk_doc_count`='$result_chk->num_rows' WHERE id = '$x'";
					$result1 = $conn->query($sql_insert);

					//---------display count of docs------------

					// $rslt[] = array('id' => $v['id'], 'type' => "child", 'text' => $v['nm'].' ('.$result->num_rows.')', 'children' => ($v['rgt'] - $v['lft'] > 1));

					$rslt[] = array('id' => $v['id'], 'type' => "child", 'text' => $v['nm'], 'children' => ($v['rgt'] - $v['lft'] > 1));
				}
				break;
			// case "get_content":
			// 	$node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : 0;
			// 	$node = explode(':', $node);
			// 	if(count($node) > 1) {
			// 		$rslt = array('content' => 'Multiple selected');
			// 	}
			// 	else {
			// 		$temp = $fs->get_node((int)$node[0], array('with_path' => true));
			// 		$rslt = array('content' => 'Current path:/' . implode('/',array_map(function ($v) { return $v['nm']; }, $temp['path'])). '/'.$temp['nm']);
			// 		$path=str_replace("Current path:/",'',$rslt['content']);
			// 		Session::put('SESS_path',$path);
			// 		//echo Session::get('SESS_path');
			// 		//exit();
			// 	}
			// 	break;
			case 'create_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$temp = $fs->mk($node, isset($_GET['position']) ? (int)$_GET['position'] : 0, array('nm' => isset($_GET['text']) ? $_GET['text'] : 'New Folder'));
				$rslt = array('id' => $temp);
				break;
			case 'rename_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$rslt = $fs->rn($node, array('nm' => isset($_GET['text']) ? trim(preg_replace('/\s*\([^)]*\)/', '', $_GET['text'])) : 'Renamed node'));
				break;
			case 'delete_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$conn = new mysqli($servername, $username, $password, $dbname);
					$sql = "SELECT doc_count,temp_doc_count,chk_doc_count FROM tree_data WHERE id = '$node'";
					$resulte = $conn->query($sql);
					while($row = $resulte->fetch_assoc()) {
        			$count=$row["doc_count"];
        			$count_temp=$row['temp_doc_count'];
        			$count_chk=$row['chk_doc_count'];    
        		}
        		if($node==1){
        			echo "root";
        			exit();
        		}
        		if($count<=0 && $count_temp<=0 && $count_chk<=0){
				$rslt = $fs->rm($node);
				break;
				}
				elseif($count_temp != 0 && $count<=0 && $count_chk<=0)
				{
					echo "temp";
					exit();
				}
				else{
				//null;	
				}
				break;
			case 'move_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? (int)$_GET['parent'] : 0;
				$rslt = $fs->mv($node, $parn, isset($_GET['position']) ? (int)$_GET['position'] : 0);
				break;
			case 'copy_node':
				$node = isset($_GET['id']) && $_GET['id'] !== '#' ? (int)$_GET['id'] : 0;
				$parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? (int)$_GET['parent'] : 0;
				$rslt = $fs->cp($node, $parn, isset($_GET['position']) ? (int)$_GET['position'] : 0);
				break;
			default:
				throw new Exception('Unsupported operation: ' . $_GET['operation']);
				break;
		}
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($rslt);
	}
	catch (Exception $e) {
		header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
		header('Status:  500 Server Error');
		echo $e->getMessage();
	}
	die();
}
?><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/documents/response.blade.php ENDPATH**/ ?>