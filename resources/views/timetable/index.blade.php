<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>TIMETABLES</h2>
            <button class="den-btn den-create-btn" onclick="createTimetableForm()">+</button>
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
        <table width="100" id="den-timetable-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>On Time</th>
                    <th>Off Time</th>
                    <th>Late Time (Minutes)</th>
                    <th>Leave Early Time (Minutes)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="den-timetable-table-body">
                @forelse($timetables as $timetable)
                    <tr id="{{ $timetable->id }}-row">
                        <td>{{ $timetable->name }}</td>
                        <td>{{ $timetable->on_time }}</td>
                        <td>{{ $timetable->off_time }}</td>
                        <td>{{ $timetable->late_time }}</td>
                        <td>{{ $timetable->leave_early_time }}</td>
                        <td>
                            <button class="den-close-button" onclick="deleteTimetable('{{ $timetable->id }}')">X</button>
                            <button class="den-edit-button" onclick="editTimetable('{{ $timetable }}')">EDIT</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No Timetables Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content modal-medium">
        <h2 class="den-modal-title">Add Timetable</h2>

        <form id="den-timetables-form" method="post" action="{{ route('timetables.store') }}">
            <input type="hidden" name="timetable_id" id="timetable_id">
            <div class="">
                <div class="den-form-group">
                    <label for="name">Name</label>
                    <input id="name" class="den-input" type="name" name="name" value="" autofocus>
                    <!-- <div class="den-error">Error messages here</div> -->
                </div>

                <div class="den-form-group">
                    <label for="late_time">Late Time (Minutes)</label>
                    <input id="late_time" class="den-input" type="number" name="late_time" value="" autofocus>
                    <!-- <div class="den-error">Error messages here</div> -->
                </div>

                <div class="den-form-group">
                    <label for="leave_early_time">Leave Early Time (Minutes)</label>
                    <input id="leave_early_time" class="den-input" type="number" name="leave_early_time" value="" autofocus>
                    <!-- <div class="den-error">Error messages here</div> -->
                </div>
            </div>

            <div class="den-form-parent-group">
                <div class="left">
                    <div class="den-form-group">
                        <label for="on_time">On Time</label>
                        <input id="on_time" class="den-input" type="time" name="on_time" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>

                <div class="right">
                    <div class="den-form-group">
                        <label for="off_time">Off Time</label>
                        <input id="off_time" class="den-input" type="time" name="off_time" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>
            </div>

            <div class="den-form-parent-group">
                <div class="left">
                    <div class="den-form-group">
                        <label for="checkin_start">Checkin Start</label>
                        <input id="checkin_start" class="den-input" type="time" name="checkin_start" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>

                <div class="right">
                    <div class="den-form-group">
                        <label for="checkin_end">Checkin End</label>
                        <input id="checkin_end" class="den-input" type="time" name="checkin_end" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>
            </div>
            
            <div class="den-form-parent-group">
                <div class="left">
                    <div class="den-form-group">
                        <label for="checkout_start">Checkout Start</label>
                        <input id="checkout_start" class="den-input" type="time" name="checkout_start" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>

                <div class="right">
                    <div class="den-form-group">
                        <label for="checkout_end">Checkout End</label>
                        <input id="checkout_end" class="den-input" type="time" name="checkout_end" value="" autofocus>
                        <!-- <div class="den-error">Error messages here</div> -->
                    </div>
                </div>
            </div>

            <div class="den-button-group">
                <button class="den-btn" id="den-form-btn">Create</button>
            </div>
        </form>

        <button class="den-close-button" onclick="closeModal()">X</button>
    </div>
</div>

@push('scripts')
<script>

    function createTimetableForm() {
        resetForm('#den-timetables-form');
        toggleModal();
    }

    function closeModal() {
        document.querySelector('#timetable_id').value = '';
        toggleModal();
    }

    function deleteTimetable(id) {
        confirmBefore('Are you sure you want to delete this timetable?').then(() => {
            fetch('/timetables/' + id, {
                method: 'DELETE',
            }).then(response => {
                return response.json();
            }).then(data => {
                if (!data.success) {
                    return toast(data.message, 'error');
                }
                
                // show message
                toast(data.message);

                // remove the timetable from the table
                document.getElementById(`${id}-row`).remove();
            }).catch(error => {
                toast(error.message, 'error');
            });
        }).catch(() => {
            // do nothing
        });
    }

    function editTimetable(timetable) {
        timetable = JSON.parse(timetable);

        document.querySelector('#timetable_id').value = timetable.id;
        document.querySelector('#name').value = timetable.name;
        document.querySelector('#late_time').value = timetable.late_time;
        document.querySelector('#leave_early_time').value = timetable.leave_early_time;
        document.querySelector('#on_time').value = timetable.on_time;
        document.querySelector('#off_time').value = timetable.off_time;
        document.querySelector('#checkin_start').value = timetable.checkin_start;
        document.querySelector('#checkin_end').value = timetable.checkin_end;
        document.querySelector('#checkout_start').value = timetable.checkout_start;
        document.querySelector('#checkout_end').value = timetable.checkout_end;
        toggleModal();
    }

</script>

<!-- todo:: move this code and other messages like error, warning, info to master layout -->
@if(session('success'))
    <script>
        toast("{{ session('success') }}");
    </script>
@endif
@endpush
</x-app-layout>
