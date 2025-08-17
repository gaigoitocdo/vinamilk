<?php
include_once __DIR__ . "/../config/config.php";
include_once __DIR__ . "/../models/UserModel.php";

$table_name = 'lottery_history';

if (isset($_POST['draw'])) {
    $db = Database::getInstance();
    $draw = field("draw", "", true, true);
    $row = field("start", "", true, true);
    $rowperpage = field("length", 10);
    $columnIndex = $_POST['order'][0]['column'];
    $columnName = $_POST['columns'][$columnIndex]['data'];
    $columnSortOrder = $_POST['order'][0]['dir'];
    $searchValue = $_POST['search']['value'];

    ## Search
    $searchQuery = " ";
    if ($searchValue != '') {
        $searchQuery = " and (`$table_name`.`sid` like '%" . $searchValue . "%' ) ";
    }

    ## Total number of records without filtering
    $totalRecords = $db->pdo_query_values("select count(1) as allcount from `$table_name` where 1");

    ## Total number of record with filtering
    $totalRecordwithFilter = $db->pdo_query_values("select count(1) as allcount from `$table_name` WHERE 1 " . $searchQuery);

    ## Fetch records - ✅ NEW: Include winning_code in query
    $empQuery = "select `$table_name`.*, `lottery`.`name` as `lottery_name`, `users`.`username` as `username`, `$table_name`.`winning_code`
                 from `$table_name` 
                 INNER JOIN lottery ON `$table_name`.`lid` = lottery.id 
                 INNER JOIN `users` ON `$table_name`.`uid` = users.id 
                 WHERE 1 " . $searchQuery. " 
                 order by `" . $columnName . "` " . $columnSortOrder . " 
                 limit " . $row . "," . $rowperpage;
    
    $empRecords = $db->pdo_query($empQuery);
    
    $data = array();
    foreach ($empRecords as $key) {
        $data[] = $key;
    }

    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "data" => $data
    );

    die(json_encode($response));
}

die("Request not from datatables, aborted.");
?>