<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>Shifts</h2>
            <button class="den-btn den-create-btn" onclick="createShiftForm()">+</button>
        </div>
        <div>
            <input class="den-input" type="search" placeholder="Search" id="searchInput">
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
        <table width="100" id="den-shifts-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th width="45%">Action</th>
                </tr>
            </thead>
            <tbody id="den-shifts-table-body">
                @forelse($shifts as $shift)
                    @php
                        if (is_string($shift->timetables)) {
                            $shift->timetables = json_decode($shift->timetables, true);
                        }

                        $isTimetableSet = false;
                        foreach($shift->timetables as $day => $timetable) {
                            if ($timetable) {
                                $isTimetableSet = true;
                            }
                        }
                    @endphp
                    <tr id="{{ $shift->id }}-row">
                        <td>
                            @if($isTimetableSet)
                                <div style="display:flex; align-items: center"><div class="circle-tick"></div> &nbsp; {{ $shift->name }}</div>
                            @else
                                {{ $shift->name }}
                            @endif
                        </td>
                        <td>
                            <button class="den-edit-button" onclick="viewTimetable('{{ $shift }}')">VIEW TIMETABLE</button>
                            <button class="den-edit-button" onclick="addTimetable('{{ $shift }}')">ADD TIMETABLE</button>
                            <button class="den-close-button" onclick="removeShift('{{ $shift->id }}')">X</button>
                            <button class="den-edit-button" onclick="editShift('{{ $shift }}')">EDIT</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No Shifts Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content modal-medium">
        <h2 class="den-modal-title">Shift</h2>
        <form id="den-shift-form" method="post" action="{{ route('shifts.store') }}">
            <input type="hidden" name="shift_id" id="shift_id">
            <div class="den-form-group">
                <label for="name">Name</label>
                <input id="name" class="den-input" type="name" name="name" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-button-group">
                <button class="den-btn" id="den-shift-form-btn">Create</button>
            </div>
        </form>

        <button class="den-close-button" onclick="closeShiftModal()">X</button>
    </div>
</div>

<div class="den-modal den-modal-large" style="visibility: hidden;">
    <div class="den-modal-content modal-large">
        
    </div>
</div>
<div class="den-modal add-timetable-modal" style="visibility: hidden;">
    <div class="den-modal-content modal-large add-timetable-modal-content">
        <!-- will add content here dynamically -->
    </div>
</div>
<div class="den-modal view-timetable-modal" style="visibility: hidden;">
    <div class="den-modal-content modal-large view-timetable-modal-content">
        <!-- will add content here dynamically -->
    </div>
</div>

@push('scripts')
    <script>
        const TIMETABLES = @json($timetables);
        function createShiftForm() {
            resetForm('#den-shift-form');
            toggleModal();
        }

        function closeShiftModal() {
            document.querySelector('#shift_id').value = '';
            toggleModal();
        }

        function removeShift(shift_id) {
            confirmBefore('Are you sure you want to delete this shift?').then(() => {
                fetch('/shifts/' + shift_id, {
                    method: 'DELETE',
                }).then(response => {
                    return response.json();
                }).then(data => {
                    if (!data.success) {
                        return toast(data.message, 'error');
                    }
                    
                    // show message
                    toast(data.message);

                    // remove the role from the table
                    document.getElementById(`${shift_id}-row`).remove();
                }).catch(error => {
                    toast(error.message, 'error');
                });
            }).catch(() => {
                console.log('User cancelled the operation');
            });
        }

        function editShift(shift) {
            shift = JSON.parse(shift);
            document.querySelector('#shift_id').value = shift.id;
            document.querySelector('#name').value = shift.name;
            toggleModal();
        }

        function addTimetable(shift)  {
            toggleModal('.add-timetable-modal');
            shift = JSON.parse(shift);
            const addTimeTableContent = generateAddTimetableContent(shift);
            document.querySelector('.add-timetable-modal-content').innerHTML = addTimeTableContent;
            timetableChange();

            const button = document.querySelector('#den-save-shift-timetable-btn');
            button.onclick = () => saveTimeTable(shift);
        }

        function closeTimetableModal() {
            toggleModal('.add-timetable-modal');
        }

        function generateAddTimetableContent(shift) {
            let table_body = ``;
            for(const day in shift.timetables){
                table_body += `<tr>`;
                table_body += `<td> ${day}</td>`;
                table_body += `<td> ${drawTimetablesSelectTag(shift.timetables[day])}</td>`;
                table_body += `</tr>`;
            }

            return `
            <div>
                <h2 class="den-modal-title">Add Timetable</h2>
                <table width="100" >
                    <thead>
                        <tr>
                            <th width="50%">Name</th>
                            <th width="50%">Timetable</th>
                        </tr>
                    </thead>
                    <tbody >
                        ${table_body}
                    </tbody>
                </table>
                <div class="den-button-group">
                    <button class="den-btn" id="den-save-shift-timetable-btn">Save</button>
                </div>    
            </div>
            
            <button class="den-close-button" onclick="closeTimetableModal()">X</button>
            `;
        }

        function drawTimetablesSelectTag(selected_timetable_id){
            let select_content = `<select name="timetable[]" onchange="timetableChange()" class="den-select">`;
            
            select_content += `<option value="">Select a Timetable</option>`;

            for(const timetable of TIMETABLES) {
                if(timetable.id == selected_timetable_id) {
                    select_content += `<option value="${timetable.id}" selected>${timetable.name}</option>`;
                    continue;
                }

                select_content += `<option value="${timetable.id}">${timetable.name}</option>`;
            }

            select_content+= `</select>`;

            return select_content;

        }

        function timetableChange() {
            const timetables_by_days = document.querySelectorAll('select[name="timetable[]"]');
            for(const timetable_by_day of timetables_by_days) {
                if(timetable_by_day.value === '') {
                    timetable_by_day.style.border = '1px solid red';
                }

                if(timetable_by_day.value !== '') {
                    timetable_by_day.style.border = '1px solid green';
                }

                if(timetable_by_day.value === 'off') {
                    timetable_by_day.style.border = '1px solid orange';
                }
            }
        }

        function saveTimeTable(shift) {
            const timetables_by_days = document.querySelectorAll('select[name="timetable[]"]');
            const days = ["MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", "SUNDAY"];
            const timetables = {};
            let iterator = 0;
            for(const timetable_by_day of timetables_by_days) {
                timetables[days[iterator++]] = timetable_by_day.value || null;
            }

            fetch(`/shifts/add-timetable/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({id: shift.id, timetables})
            }).then(response => {
                return response.json();
            }).then(data => {
                if (!data.success) {
                    return toast(data.message, 'error');
                }
                
                // show message
                toast(data.message);
                setTimeout(() => {
                    location.reload();
                }, 500);
            }).catch(error => {
                toast(error.message, 'error');
            });
        }

        function viewTimetable(shift) {
            toggleModal('.view-timetable-modal');
            shift = JSON.parse(shift);
            const viewTimeTableContent = generateViewTimetableContent(shift);
            document.querySelector('.view-timetable-modal-content').innerHTML = viewTimeTableContent;
        }

        function generateViewTimetableContent(shift) {
        let table_body = ``;
        for(const day in shift.timetables){ 
            const timetable_id = shift.timetables[day];
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
        }

        return `
        <div>
            <h2 class="den-modal-title">View Timetable</h2>
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
        
        <button class="den-close-button" onclick="closeViewtableModal()">X</button>
        `;
    }

    function closeViewtableModal() {
        toggleModal('.view-timetable-modal');
    }

    </script>

    @if(session('success'))
        <script>
            toast("{{ session('success') }}");
        </script>
    @endif
@endpush
</x-app-layout>
