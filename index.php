<?php
    require_once('sql.php');
    $sql = new SqlWork();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./style.css">
    <title>Crud</title>
</head>
<body>
    <div id="main_container" class="custom-font px-5">
        <main class="container-sm py-3 d-flex flex-column my-5" id="container_table">

            <div class="py-2">
                <h2>Tasks list</h2>
            </div>

            <div class="py-2">
                <button id="container_button"  class="btn btn-primary">Add Task</button>
            </div>

            <table class="table table-bordered" id="table">
                <thead>
                    <tr>
                        <th>
                            Task name
                        </th>
                        <th>
                            Task description
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody id="table_body">
                    <?php
                        $array = $sql->getAll();
                        if($array){
                            foreach ($array as $value){
                    ?>
                            <tr data-task-id="<?php echo $value['id']; ?>">
                                <td class="taskName" ><?php echo $value['task_name']; ?></td>
                                <td class="taskDescription" ><?php echo $value['task_description']; ?></td>
                                <td class="taskDate" ><?php echo $value['task_date']; ?></td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <button data-edit-btn="<?php echo $value['id']; ?>" class="border px-3 rounded-start edit-button">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <button data-del-btn="<?php echo $value['id']; ?>" class="border px-1 rounded-end border-start-0 delete-button">
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
        </main>



        <!--   Forms     -->
        <div id="form_delete-submit" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="submit-delete_form">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete task</h5>
                            <button type="button" id="close_modal_delete" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input name="id_delete_task" type="hidden" value="" id="id_delete_task">
                            <span class="custom-font fs-6">
                                Are you sure you want delete Task: <b id="task_name"></b>, created at: <b id="task_date"></b>?
                            </span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button id="delete_submit" type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--   I use 1 form for add and change  -->
        <div id="form_actions" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="submit_action">
                        <div class="modal-header">
                            <h5 id="form_title" class="modal-title"></h5>
                            <button type="button" id="close_modal_action" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex flex-column">

                            <input type="hidden" name="id_task" value="">

                            <label class="fs-6" for="task_name">Task name</label>
                            <input class="border p-2" name="task_name" id="task_name" type="text">
                            <span id="error_name" class="mb-4 text-danger"></span>

                            <label class="fs-6" for="task_description">Task description</label>
                            <input class="border p-2" name="task_description" id="task_description" type="text">
                            <span id="error_description" class="mb-4 text-danger"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button name="submit_form" type="submit" id="submit_action_form" class="btn btn-primary"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="form_check" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Warning</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="custom-font fs-6">This task wasn't exists!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/js/bootstrap.min.js"></script>
<script src="script.js" defer></script>
</body>
</html>