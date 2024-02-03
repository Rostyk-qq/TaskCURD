<?php


class SqlWork
{
    private $server = 'localhost';
    private $username = 'root';
    private $password = '';
    private $db_name = 'tasks_db';

    private $connection;

    public function __construct()
    {
        $this->connection = $this->connectDb();
        if (!empty($this->connection)) {
            $this->selectDb();
        }
    }

    public function connectDb()
    {
        $conn = new mysqli($this->server, $this->username, $this->password, $this->db_name);
        return $conn;
    }

    public function selectDb()
    {
        mysqli_select_db($this->connection, $this->db_name);
    }

    protected function addTask($task){
        $add = $this->connection->prepare("INSERT INTO `tasks` (`task_name`, `task_description`, `task_date`) VALUES (?, ?, ?)");
        $add->bind_param('sss', $task['task_name'], $task['task_description'], $task['task_date']);

        if($add->execute() !== TRUE){
            http_response_code(500);
            return json_encode(['status' => false, 'error' => ['code' => 101, 'message' => 'SQL: task wasn\'t added!']]);
        }
        $add->close();
    }
    protected function updateTask($task){
        $update = $this->connection->prepare('UPDATE `tasks` SET task_name=?, task_description=?, task_date=? where id=?');
        $id = (int)$task['id_task'];
        $update->bind_param('sssi', $task['task_name'], $task['task_description'], $task['task_date'], $id);

        if($update->execute() !== TRUE){
            http_response_code(500);
            return json_encode(['status' => false, 'error' => ['code' => 102, 'message' => 'SQL: task wasn`t updated!']]);
        }
        $update->close();
    }
    protected function getTask($id){
        if(empty($id)){
            $get = "SELECT * FROM `tasks` ORDER BY id DESC LIMIT 1";
            $result = $this->connection->query($get);
            if($result->num_rows > 0){
                $data = $result->fetch_assoc();
                $id = $data['id'];
            }
        }

        $getTask = $this->connection->prepare("SELECT * FROM `tasks` WHERE id = ?");
        $id_task = (int)$id;
        $getTask->bind_param('i', $id_task);

        if($getTask->execute() === TRUE){
            $result = $getTask->get_result();
            $data = $result->fetch_assoc();

            $getTask->close();

            if(empty($data)){
                http_response_code(500);
                return json_encode(['status' => false, 'error' => ['code' => 103, 'message' => 'This user do not exists!']]);
            }
            else{
                return json_encode(['status' => true, 'error' => null, 'user' => $data]);
            }
        }
    }

    protected function deleteTask($id){
        $delete = $this->connection->prepare('DELETE FROM `tasks` WHERE id = ?');
        $delete->bind_param('i', $id);

        if($delete->execute() !== TRUE){
            http_response_code(500);
            return json_encode(['status' => false, 'error' => ['code' => 104, 'message' => 'SQL: task wasn\'t exists']]);
        }
        else{
            $delete->close();
            return json_encode(['status' => true, 'error' => null, 'id' => $id]);
        }
    }

    protected function checkTask($id){
        $check = $this->connection->prepare('SELECT * FROM `tasks` WHERE id = ?');
        $check->bind_param('i', $id);

        if($check->execute() === TRUE){
            $result = $check->get_result();
            $data = $result->fetch_assoc();
            if($data){
                return json_encode(['message' => 'exists']);
            }
            else{
                return json_encode(['message' => 'empty']);
            }
        }
        else{
            http_response_code(500);
            return json_encode(['status' => false, 'error' => ['code' => 105, 'message' => 'This task cannot exists!']]);
        }
    }

    public function getAll(): array{
        $selectAll = $this->connection->prepare("SELECT * from `tasks`;");
        $selectedAll = [];
        if($selectAll->execute() === TRUE){
            $result = $selectAll->get_result();

            while($data = $result->fetch_assoc()){
                $selectedAll[] = $data;
            }
        }
        return $selectedAll;
    }
}

$sqlWork = new SqlWork();










































