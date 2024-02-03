window.addEventListener('DOMContentLoaded', async function(e){

    // get elements
    const listTasks = document.getElementById('table_body');

    const modals = {
        modalDelete: new bootstrap.Modal(document.getElementById('form_delete-submit')),
        modalActions: new bootstrap.Modal(document.getElementById('form_actions')),
        modalCheck: new bootstrap.Modal(document.getElementById('form_check')),
    }

    const forms = {
        formDeleteSubmit: document.getElementById('submit-delete_form'),
        formSubmitAction: document.getElementById('submit_action'),
    }

    const formDeleteParams = {
        taskName: document.getElementById('task_name'),
        taskDate: document.getElementById('task_date'),
        inputForId: document.getElementById('id_delete_task')
    }

    const formActionsParams = {
        title: document.getElementById('form_title'),
        submitButton: document.getElementById('submit_action_form')
    }

    const formButtonsForTrigger = {
        closeFormAction: document.getElementById('close_modal_action'),
        closeFormDelete: document.getElementById('close_modal_delete'),
    }

    const buttons = {
        buttonAddTask: document.getElementById('container_button'),
        allDeleteButtons: document.querySelectorAll('.delete-button'),
        allEditButtons: document.querySelectorAll('.edit-button'),
    }

    const buttonErrors = {
        name: document.getElementById('error_name'),
        description: document.getElementById('error_description')
    }

    // create logic
    buttons.buttonAddTask.onclick = (e) => {
        e.preventDefault();
        forms.formSubmitAction.reset();

        formActionsParams.title.textContent = 'Add task';
        formActionsParams.submitButton.textContent = 'Add';

        forms.formSubmitAction.elements['id_task'].value = '';

        forms.formSubmitAction.reset();

        modals.modalActions.show();
    };

    buttons.allEditButtons.forEach(btnEdit => {
        forms.formSubmitAction.reset();
        EditTask(btnEdit);
    });
    buttons.allDeleteButtons.forEach(btnDelete => {
        DeleteTask(btnDelete);
    });


    // validate errors
    function ValidateErrors(code){
        switch(code){
            case 1:
                return 'error_name';
            case 2:
                return "error_description";
        }
    }
    function ClearErrors(){
        buttonErrors.name.textContent = '';
        buttonErrors.description.textContent = '';
    }

    // submit form for (add, update)
    forms.formSubmitAction.onsubmit = async (e) => {
        e.preventDefault();

        formActionsParams.submitButton.disable = true;
        const date = new Date()

        const day = date.getDate() < 10 ? `0${date.getDate()}` : date.getDate();
        const month = date.getMonth() < 10 ? `0${date.getMonth()}` : date.getMonth();

        const minutes = date.getMinutes() < 10 ? `0${date.getMinutes()}` : date.getMinutes();
        const seconds = date.getSeconds() < 10 ? `0${date.getSeconds()}` : date.getSeconds();

        const data = {
            id_task: forms.formSubmitAction.elements['id_task'].value,
            task_name: forms.formSubmitAction.elements['task_name'].value,
            task_description: forms.formSubmitAction.elements['task_description'].value,
            task_date: date.getFullYear() + '-' + month + '-' + day + ' ' + date.getHours() + ':' + minutes + ':' + seconds
        }

        const response = await postData(data).then(response => response.json());
        // clear old errors for set new if exists
        ClearErrors();
        try{
            if (response.errors && response.status === false) {
                for (const value of response.errors) {
                    let path = ValidateErrors(value.code);
                    document.getElementById(`${path}`).textContent = value.message;
                }
            }
            else{
                if(forms.formSubmitAction.elements['id_task'].value === ''){
                    addTask(response.user);
                    bindEdit(response.user.id);
                    bindDelete(response.user.id);
                }
                else{
                    updateTask(response.user);
                }

                formActionsParams.submitButton.disable = false;
                formButtonsForTrigger.closeFormAction.click();
                forms.formSubmitAction.reset();
            }
        }
        catch(e){
            console.error(e, 'Error');
        }
    }
    // delete submit
    forms.formDeleteSubmit.onsubmit = async (e) => {
        e.preventDefault();
        const getId = parseInt(formDeleteParams.inputForId.value);
        const data = {id: getId, action: 'delete'};

        try{
            const response = await postData(data).then(response => response.json());
            if(response.error && response.status === false){
                console.log(response.error.message);
            }
            else{
                deleteTask(parseInt(response.id));
            }
            formButtonsForTrigger.closeFormDelete.click();
        }
        catch (e){
            console.error(e, 'Error');
        }
    }

    // check if task exists before update
    async function checkAfterUpdate(data){
        const response = await postData(data).then(response => response.json());
        if(response.error){
            console.log(response.error.message);
        }
        else{
            return response.message === 'exists';
        }
    }

    // ajax use only post method
    async function postData(data){
        const url = 'http://localhost/Task/main.php';
        return await fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        })
    }


    // Front add, update, delete
    function addTask(taskObject){
        const newTask = `
            <tr data-task-id="${taskObject.id}">
                <td class="taskName" >${taskObject.task_name}</td>
                <td class="taskDescription">${taskObject.task_description}</td>
                <td class="taskDate">${taskObject.task_date}</td>
                <td>
                    <div class="d-flex align-items-center justify-content-center">
                       <button data-edit-btn="${taskObject.id}" class="border px-3 rounded-start edit-button">
                            <i class="fa-regular fa-pen-to-square"></i>
                       </button>
                       <button data-del-btn="${taskObject.id}" class="border px-1 rounded-end border-start-0 delete-button">
                            <i class='fa fa-trash'></i>
                       </button>
                    </div>
                </td>
            </tr>`;

        listTasks.insertAdjacentHTML("beforeend", newTask);
    }
    function updateTask(taskObject){
        const getTaskForUpdate = document.querySelector(`#table_body tr[data-task-id="${parseInt(taskObject.id)}"]`);
        if(getTaskForUpdate){
            getTaskForUpdate.querySelector('.taskName').textContent = taskObject.task_name;
            getTaskForUpdate.querySelector('.taskDescription').textContent = taskObject.task_description;
            getTaskForUpdate.querySelector('.taskDate').textContent = taskObject.task_date;
        }
    }
    function deleteTask(id){
        const task = document.querySelector(`#table_body tr[data-task-id="${parseInt(id)}"]`);
        if(task){
            task.remove();
        }
    }

    // after add new task, we add update event for Edit end Delete
    function bindEdit(id){
        const btnEdit = document.querySelector(`#table_body tr button[data-edit-btn="${parseInt(id)}"]`);
        if(btnEdit){
            EditTask(btnEdit);
        }
    }
    function bindDelete(id){
        const btnDelete = document.querySelector(`#table_body tr button[data-del-btn="${parseInt(id)}"]`);
        if(btnDelete){
            DeleteTask(btnDelete);
        }
    }

     function EditTask(btnEdit){
        if(btnEdit) {
            btnEdit.onclick = async (e) => {
                e.preventDefault();

                formActionsParams.title.textContent = 'Edit task';
                formActionsParams.submitButton.textContent = 'Edit';

                const idFromBtn = parseInt(btnEdit.getAttribute('data-edit-btn'));

                const data = {id: idFromBtn, action: 'check'};
                const res = await checkAfterUpdate(data);
                
                if(res){
                    const taskRow = document.querySelector(`#table_body tr[data-task-id="${idFromBtn}"]`);

                    forms.formSubmitAction.elements['id_task'].value = idFromBtn;
                    forms.formSubmitAction.elements['task_name'].value = taskRow.querySelector('.taskName').textContent;
                    forms.formSubmitAction.elements['task_description'].value = taskRow.querySelector('.taskDescription').textContent;

                    modals.modalActions.show();
                }
                else{
                    modals.modalCheck.show();
                }
            }
        }
    }
    function DeleteTask(btnDelete){
        if(btnDelete){
            btnDelete.onclick = (e) => {
                const btnDeleteId = parseInt(btnDelete.getAttribute('data-del-btn'));
                const taskRow = document.querySelector(`#table_body tr[data-task-id="${btnDeleteId}"]`);

                formDeleteParams.inputForId.value = btnDeleteId;
                formDeleteParams.taskName.textContent = taskRow.querySelector('.taskName').textContent;
                formDeleteParams.taskDate.textContent = taskRow.querySelector('.taskDate').textContent;

                modals.modalDelete.show();
            }
        }
    }
})