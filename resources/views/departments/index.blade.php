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
                    <tr id="{{ $department->id }}-row" class="parent-dep-row">
                        <td colspan="3">{{ $department->name }}</td>
                    </tr>
                    @forelse($department->children as $subDepartment)
                        <tr id="{{ $subDepartment->id }}-row" >
                            <td>{{ $subDepartment->name }}</td>
                            <td>{{ $subDepartment->users_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No Sub Departments Found</td>
                        </tr>
                    @endforelse
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
            <input type="hidden" id="department_id">
            <div class="den-form-group">
                <label for="department_type">Select Type</label>
                <select name="department_type" class="den-select" id="department_type">
                    <option value="">Select Department Type</option>
                    <option value="DEPARTMENT">Department</option>
                    <option value="SUBDEPARTMENT">Sub Department</option>
                </select>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group" id="department_input_name_div" style="visibility: hidden;">
                <label for="name">Name</label>
                <input id="name" class="den-input" type="name" name="name" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group" id="department_input_parent_div" style="visibility: hidden;">
                <label for="parent_department">Select a Parent Department</label>
                <select name="parent_id" class="den-select" id="parent_departments">
                    <option value="">Select Parent Department</option>
                    @forelse($parentDepartments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @empty
                        <option value="">No Departments Found</option>
                    @endforelse
                </select>
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

    // Event listener for department type select
    document.querySelector('#department_type').addEventListener('change', (e) => {
        const departmentType = e.target.value;
        if(departmentType === 'DEPARTMENT') {
            document.getElementById('department_input_name_div').style.visibility = 'visible';
            document.getElementById('department_input_parent_div').style.visibility = 'hidden';
        } else if(departmentType === 'SUBDEPARTMENT') {
            document.getElementById('department_input_name_div').style.visibility = 'visible';
            document.getElementById('department_input_parent_div').style.visibility = 'visible';
        } else {
            document.getElementById('department_input_name_div').style.visibility = 'hidden';
            document.getElementById('department_input_parent_div').style.visibility = 'hidden';
        }
    });
</script>
@endpush

</x-app-layout> 
