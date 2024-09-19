<style>
    .den-department {
        display: flex;
    }
    .den-department:hover {
        cursor: pointer;
    }
</style>
<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>Employee Transfer</h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div>
        <div style="display: flex;">
            <div id="den-employee-transfer-left-section" style="width: 50%; box-sizing: border-box; padding: 10px; border-right: solid 1px #000;">
                @forelse($departments as $department)
                    <div class="den-department" onclick="getUsers('{{ $department->id }}', this)">
                        {{ $department->name }}<span class="badge" style="margin-left: 5px; margin-top: 5px">{{ $department->users_count }}</span>
                    </div>
                @empty
                    <p>No Departments Found</p>
                @endforelse
            </div>
            <div style="width: 50%; box-sizing: border-box; padding: 10px;">
                <button class="den-btn" onclick="transferModal()">Transfer</button><br><br>
                <table width="100" id="den-employee-transfer-table">
                    <thead>
                        <tr>
                            <th width="10%">
                                <input type="checkbox" id="all_checkbox" name="all_checkbox" value="all_checkbox" onclick="checkUncheckAll(this)">
                            </th>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="den-employee-transfer-table-body"></tbody>
                </table>
            </div>
        </div>          
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content">
        <h2 class="den-modal-title">Employee Transfer</h2>
        <div id="den-employee-transfer-left-section-modal" style="padding: 15px;">
            <select name="department" class="den-select" id="department">
                <option value="">Select Department</option>
                @forelse($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @empty
                    <option value="">No Departments Found</option>
                @endforelse
            </select>
        </div>
        <div class="den-button-group">
            <button class="den-btn" onclick="transfer()">Transfer</button>
        </div>
        <button class="den-close-button" onclick="closeModal()">X</button>
    </div>
</div>

@push('scripts')
<script>
    function getUsers(departmentId, currentElement) {
        // Reset all departments background color and text color
        const departmentsDivs = document.querySelectorAll('.den-department');
        departmentsDivs.forEach(departmentDiv => {
            departmentDiv.style.backgroundColor = '#fff';
            departmentDiv.style.color = '#181616';
        });

        // Set the current department background color and text color
        currentElement.style.backgroundColor = '#242e42';
        currentElement.style.color = '#fff';

        // checkbox using for all checkbox make unchecked
        document.getElementById('all_checkbox').checked = false;

        // Fetch users by department
        fetch('{{route("departments.users-by-department")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                department_id: departmentId
            })
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(!data.success) {
                resetTransferTable();
                return toast(data.message, 'error');
            }

            let users = data.users;
            let html = '';
            users.forEach(user => {
                html += `<tr>
                    <td><input type="checkbox" onclick="checkAllCheckboxes()" name="user_id[]" value="${user.id}"></td>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                </tr>`;
            });
            document.getElementById('den-employee-transfer-table-body').innerHTML = html;
        }).catch((error) => {
            console.error('Error:', error);
        });
    }

    function checkUncheckAll(element) {
        const checkboxes = document.getElementsByName('user_id[]');
        if(element.checked) {
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function checkAllCheckboxes() {
        const checkboxes = document.getElementsByName('user_id[]');
        let allChecked = true;
        checkboxes.forEach(checkbox => {
            if(!checkbox.checked) {
                document.getElementById('all_checkbox').checked = false;
                allChecked = false;
                return;
            }
        });

        if(allChecked) document.getElementById('all_checkbox').checked = true;
    }

    // reset transfer table
    function resetTransferTable() {
        const tableBody = document.querySelector("#den-employee-transfer-table-body");

        // Remove all rows in the table body except the first row (header)
        while (tableBody.lastElementChild) {
            tableBody.removeChild(tableBody.lastElementChild);
        }
    }

    function transferModal() {
        const checkboxes = document.getElementsByName('user_id[]');
        let users = [];
        checkboxes.forEach(checkbox => {
            if(checkbox.checked) {
                users.push(checkbox.value);
            }
        });

        if(!users.length) {
            toast('Please select users', 'warning');
            return;
        }

        toggleModal();
    }

    function closeModal() {
        document.getElementById('department').value = "";
        toggleModal();
    }

    function transfer() {
        const checkboxes = document.getElementsByName('user_id[]');
        let users = [];
        checkboxes.forEach(checkbox => {
            if(checkbox.checked) {
                users.push(checkbox.value);
            }
        });

        if(!users.length) {
            toast('Please select users', 'warning');
            return;
        }

        const department = document.getElementById('department').value;

        if(!department) {
            toast('Please select department', 'warning');
            return;
        }

        fetch('{{route("employee-transfers.action")}}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                users,
                department
            })
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(!data.success) {
                return toast(data.message, 'error');
            }

            toast(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }).catch((error) => {
            console.error('Error:', error);
        });
    }
</script>
@endpush
</x-app-layout>
