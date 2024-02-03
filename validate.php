<?php

require_once('sql.php');
class ValidateParams extends SqlWork {
    public function validate($taskName, $taskDescription, $taskDate){
        $errors = [];

        if (empty($taskName)) {
            $errors[] = ['code' => 1, 'message' => 'You cannot leave the task name field empty!'];
        }
        if (empty($taskDescription)) {
            $errors[] = ['code' => 2, 'message' => 'You cannot leave the task description field empty!'];
        }
        if (!empty($errors)) {
            echo json_encode(['status' => false, 'errors' => $errors]);
            die();
        }
    }
    public function validateAction($data){
        if(isset($data['action'])){
            if($data['action'] === 'delete'){
                echo $this->deleteTask((int)$data['id']);
            }
            if($data['action'] === 'check'){
                echo $this->checkTask((int)$data['id']);
            }
        }
        else{
            $taskName = htmlspecialchars(trim($data['task_name']));
            $taskDescription = htmlspecialchars(trim($data['task_description']));
            $taskDate = htmlspecialchars(trim($data['task_date']));

            $this->validate($taskName, $taskDescription, $taskDate);


            if(empty($data['id_task'])){
                $this->addTask($data);
                echo $this->getTask((int)$data['id_task']);
            }
            else{
                $this->updateTask($data);
                echo $this->getTask((int)$data['id_task']);
            }
        }
    }
}