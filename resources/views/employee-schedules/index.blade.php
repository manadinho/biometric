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
            <h2>Employee Schedule</h2>
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
                            <th>Timetables</th>
                        </tr>
                    </thead>
                    <tbody id="den-employee-transfer-table-body"></tbody>
                </table>
            </div>
        </div>           
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal den-modal-first" style="visibility: hidden;">
    <div class="den-modal-content modal-large">
        <h2 class="den-modal-title">Employee Schedule</h2>
        <div id="employee-schedule-select-tag-section" style="padding: 15px;">
            <label for="shift">Select Shift</label>
            <select name="shift" onchange="shiftChange()" class="den-select">
                <option value="">Select Shift</option>
                @forelse($shifts as $shift)
                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                @empty
                    <option value="">No Shifts Found</option>
                @endforelse
            </select>
            <div class="den-form-parent-group">
                <div class="left">
                    <div class="den-form-group">
                        <label for="start_date">Start Date</label>
                        <input id="start_date" class="den-input" type="date" name="start_date" onchange="dateChange(this)" value="" autofocus>
                    </div>
                </div>

                <div class="right">
                    <div class="den-form-group">
                        <label for="end_date">End Date</label>
                        <input id="end_date" class="den-input" type="date" name="end_date" value="" onchange="dateChange(this)" autofocus>
                    </div>
                </div>
            </div>
        </div>
        <div id="employee-schedule-timetable-draw-section"></div>
        <div class="den-button-group">
            <button class="den-btn" onclick="arrangeShift()">Done</button>
        </div>
        <button class="den-close-button" onclick="toggleModal('.den-modal-first')">X</button>
    </div>
</div>

<!--SECOND MODAL SECTION -->
<div class="den-modal den-modal-second" style="visibility: hidden;">
    <div class="den-modal-content modal-large den-modal-content-second">
        <h2 class="den-modal-title">Employee Schedule</h2>
        <div id="modal-shift-detail-section"></div>
        <button class="den-close-button" onclick="toggleModal('.den-modal-second')">X</button>
    </div>
</div>

<script>
    const TIMETABLES = @json($timetables);

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

        toggleModal('.den-modal-first');
    }
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
                department_id: departmentId,
                withShifts: true
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
                const shiftContent = drawShifts(user.shifts);
                html += `<tr>
                    <td><input type="checkbox" onclick="checkAllCheckboxes()" name="user_id[]" value="${user.id}"></td>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${shiftContent}</td>
                </tr>`;
            });
            document.getElementById('den-employee-transfer-table-body').innerHTML = html;
        }).catch((error) => {
            console.error('Error:', error);
        });

        function drawShifts(shifts) {
            if(shifts.length === 0) {
                return 'No shifts found';
            }

            let content = '<div style="display:flex">';
            for(const shift of shifts) {
                content += `<span class="den-secondary-badge user-shift-badge" style="width:250px">
                        🕒 &nbsp; ${shift.name}
                        <br>
                        ${formatDate(shift.pivot.start_date)} to ${formatDate(shift.pivot.end_date)}
                        <br>
                        <span style="display:flex">
                            <span class="den-primary-badge user-shift-badge-btn" onclick='showShiftDetails(${JSON.stringify(shift)})'>view</span>
                            <span class="den-primary-badge user-shift-badge-btn">edit</span>
                            <span class="den-primary-badge user-shift-badge-btn" onclick="deleteUserShift(${shift.id}, this)">delete</span>
                        </span>
                    </span>
                    <br>`;
            }
            content += '</div>';

            return content;
        }
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

    function arrangeShift() {
        const shift_id = document.querySelector('select[name="shift"]').value;
        const start_date = document.querySelector('input[name="start_date"]').value;
        const end_date = document.querySelector('input[name="end_date"]').value;
        
        if(!shift_id || !start_date || !end_date) {
            toast('Please fill all fields', 'warning');
            return;
        }

        if(start_date > end_date) {
            toast('Start date cannot be greater than end date', 'warning');
            return;
        }

        const users = getSelectedUsers();
        if(!users.length) {
            toast('Please select users', 'warning');
            return;
        }

        fetch('{{ route("employee-schedules.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                users,
                shift_id,
                start_date,
                end_date
            })
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if(!data.success) {
                return toast(data.message, 'error');
            }

            toast(data.message);
            setTimeout(() => {
                location.reload();
            }, 500);
        }).catch((error) => {
            console.error('Error:', error);
        });

        // let values = users.map(user => `(${user}, ${shift_id}, '${start_date}', '${end_date}', 1)`).join(',');
        // console.log(values);
    }

    function getSelectedUsers() {
        const checkboxes = document.getElementsByName('user_id[]');
        let users = [];
        checkboxes.forEach(checkbox => {
            if(checkbox.checked) {
                users.push(checkbox.value);
            }
        });

        return users;
    }

    function showShiftDetails(data) {
        const modal = document.querySelector('.den-modal-second');
        modal.style.visibility = modal.style.visibility == 'visible' ? 'hidden' : 'visible';

        let content = `
            <h3>Shift Name : <small>${data.name}</small></h3>
            <h3>Start Date : <small>${formatDate(data.pivot.start_date)}</small></h3>
            <h3>End Date : <small>${formatDate(data.pivot.end_date)}</small></h3>
        `;

        content += generateViewTimetableContent(data);
        document.querySelector('#modal-shift-detail-section').innerHTML = content;
    }

    function generateViewTimetableContent(shift) {
        let table_body = ``;
        let count = 0;
        for(const day in JSON.parse(shift.timetables)) { 
            const timetable_id = JSON.parse(shift.timetables)[day];
            const timetable = TIMETABLES.find(timetable => timetable.id == timetable_id);
            const start = timetable ? + timetable.on_time.split(':')[0] : null;
            const end = timetable ? + timetable.off_time.split(':')[0] : null;

            table_body += `<tr>`;
            table_body += `<td> ${day}</td>`;
            for(let i = 0; i < 24; i++) {
                if(!start) {
                    table_body += `<td class="timetable-red"></td>`;
                    continue;
                }
                if(i >= start && i <= end) {
                    table_body += `<td class="timetable-highlight"></td>`;
                    continue;
                }
                table_body += `<td></td>`;
            }
            table_body += `</tr>`;
            count++;
        }

        return `
        <div>
            <table width="100" >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>0</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                        <th>8</th>
                        <th>9</th>
                        <th>10</th>
                        <th>11</th>
                        <th>12</th>
                        <th>13</th>
                        <th>14</th>
                        <th>15</th>
                        <th>16</th>
                        <th>17</th>
                        <th>18</th>
                        <th>19</th>
                        <th>20</th>
                        <th>21</th>
                        <th>22</th>
                        <th>23</th>
                    </tr>
                </thead>
                <tbody >
                    ${table_body}
                </tbody>
            </table> 
        </div>
        `;
    }
</script>

    @if(session('success'))
        <script>
            toast("{{ session('success') }}");
        </script>
    @endif
</x-app-layout>
