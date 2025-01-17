<style>
    .fingerprint-required{
        background-color: #f8d7da !important;
    }

    .fingerprint-icon:hover{
        cursor: pointer;
    }
</style>
<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>SETTINGS</h2>
        </div>
        <div>
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
    
    <div style="display: flex; justify-content: space-around;">
        <div>
            <button class="den-btn" onclick="syncTimeWithMachine()">Sync Time With Machine</button>
        </div>
        <div>
            <button class="den-btn-danger" onclick="deleteAttendance()">Delete Attendance</button>
        </div>
        <div>
            <button class="den-btn-danger" onclick="deleteEmployees()">Delete Employees</button>
        </div>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content">
        <h2 class="den-modal-title">Add User</h2>

        <button class="den-close-button" onclick="closeModal()">X</button>
    </div>
</div>

@push('scripts')

<script>

    function syncTimeWithMachine(){
        const date = new Date();
        const formattedDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`;
        if(window.DENONTEK_SOCKET.readyState === WebSocket.OPEN) {
            window.DENONTEK_SOCKET.send('S' + formattedDate);
        } else {
            toast('Socket is not connected.', 'error');
        }
    }

    function deleteAttendance(){
        confirmBefore('Are you sure you want to delete all attendance?').then(() => {
            if(window.DENONTEK_SOCKET.readyState === WebSocket.OPEN) {
                window.DENONTEK_SOCKET.send('a');
            } else {
                toast('Socket is not connected.', 'error');
            }
        });
    }

    function deleteEmployees(){
        confirmBefore('Are you sure you want to delete all employees?').then(() => {
            if(window.DENONTEK_SOCKET.readyState === WebSocket.OPEN) {
                window.DENONTEK_SOCKET.send('e');
            } else {
                toast('Socket is not connected.', 'error');
            }
        });
    }
    

</script>

@endpush
</x-app-layout>
