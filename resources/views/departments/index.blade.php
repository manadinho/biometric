<style>
    .parent-dep-row{
        background-color: #242e42 !important;
        color: white;
    }
</style>
<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>DEPARTMENTS</h2>
            <button class="den-btn den-create-btn" onclick="createDepartmentForm()">+</button>
        </div>
        <div>
            <input class="den-input" type="search" placeholder="Search" id="searchInput">
        </div>
    </div>
    
    <div>

        <table width="100" id="den-departments-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Total Employees</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody id="den-departments-table-body">
                @forelse($departments as $department)
                    <tr id="{{ $department->id }}-row">
                        <td>{{ $department->name }}</td>
                        <td><span class="den-primary-badge">{{ $department->users_count }}</span></td>
                        <td>
                            <button class="den-close-button" onclick="removeDepartment('{{ $department->id }}')">X</button>
                            <button class="den-edit-button" onclick="editDepartment('{{ $department }}')">EDIT</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No Departments Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content">
        <h2 class="den-modal-title">Department</h2>

        <form id="den-department-form" action="{{ route('departments.store') }}" method="post">
            <input type="hidden" id="department_id" name="department_id">
            <div class="den-form-group" id="department_input_name_div" >
                <label for="name">Name</label>
                <input id="name" class="den-input" type="name" name="name" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>
            <div class="den-button-group">
                <button class="den-btn" id="den-department-form-btn">Create</button>
            </div>
        </form>

        <button class="den-close-button" onclick="closeModal()">X</button>
    </div>
</div>

@push('scripts')
<script>
    function createDepartmentForm() {
        resetForm('#den-department-form');
        toggleModal();
    }

    function closeModal() {
        document.querySelector('#department_id').value = '';
        toggleModal();
    }

    function removeDepartment(id) {
        confirmBefore('Are you sure you want to delete this Department?').then(() => {
            fetch('/departments/' + id, {
                method: 'DELETE',
            }).then(response => {
                return response.json();
            }).then(data => {
                // check status code
                if (!data.success) {
                    return toast(data.message, 'error');
                }
                // show message
                toast(data.message);

                // remove the role from the table
                document.getElementById(`${id}-row`).remove();
            }).catch(error => {
                toast(error.message, 'error');
            });
        }).catch(() => {
            console.log('User cancelled the operation');
        });
    }

    function editDepartment(department) {
        department = JSON.parse(department);
        console.log('department', department);
        resetForm('#den-department-form');
        toggleModal();
        document.querySelector('#department_id').value = department.id;
        document.querySelector('#name').value = department.name;
    }
</script>
@endpush

</x-app-layout> 
