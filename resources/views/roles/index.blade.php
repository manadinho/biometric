<x-app-layout>
<div class="container" id="app">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>ROLES</h2>
            <button class="den-btn den-create-btn" onclick="createRoleForm()">+</button>
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
        <table width="100" id="den-roles-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody id="den-roles-table-body">
                @forelse($roles as $role)
                    <tr id="{{ $role->id }}-row">
                        <td>{{ $role->name }}</td>
                        <td>
                            @forelse($role->permissions as $permission)
                                <span class="den-primary-badge" >{{ $permission->name }}</span>
                            @empty
                                <span class="den-secondary-badge">No Permissions</span>
                            @endforelse
                        </td>
                        <td>
                            <button class="den-close-button" onclick="removeRole('{{ $role->id }}')">X</button>
                            <button class="den-edit-button" onclick="editRole('{{ $role }}')">EDIT</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No Roles Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- MODAL SECTION -->
    <div class="den-modal" style="visibility: hidden;">
        <div class="den-modal-content">
            <h2 class="den-modal-title">Add Role</h2>

            <form id="den-role-form" method="post" action="{{ route('roles.store') }}">
                @csrf
                <input type="hidden" name="id" id="role_id">
                <div class="den-form-group">
                    <label for="name">Name</label>
                    <input id="name" class="den-input" type="name" name="name" v-model="role_name" autofocus>
                    <!-- <div class="den-error">Error messages here</div> -->
                </div>

                <div class="den-form-group">
                    <label for="permissions">Select a Permissions</label>
                    <select name="permissions[]" class="den-select" id="permissions" multiple>
                        @forelse($permissions as $permission)
                            <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                        @empty
                            <option value="">No Permissions Found</option>
                        @endforelse
                    </select>
                    <!-- <div class="den-error">Error messages here</div> -->
                </div>

                <div class="den-button-group">
                    <button class="den-btn" @click="createRole">Create</button>
                </div>
            </form>

            <button class="den-close-button" onclick="closeModal()">X</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function createRoleForm() {
        resetForm('#den-role-form');
        toggleModal();
    }

    function closeModal() {
        document.querySelector('#role_id').value = '';
        toggleModal();
    }

    function removeRole(id) {
        confirmBefore('Are you sure you want to delete this role?').then(() => {
            fetch('/roles/' + id, {
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
                document.getElementById(`${id}-row`).remove();
            }).catch(error => {
                toast(error.message, 'error');
            });
        }).catch(() => {
            console.log('User cancelled the operation');
        });
    }

    function editRole(role) {
        role = JSON.parse(role);
        resetForm('#den-role-form');
        toggleModal();
        document.querySelector('#role_id').value = role.id;
        document.querySelector('#name').value = role.name;
        const permissions = role.permissions.map(permission => permission.id);

        var selectElement = document.querySelector('select[name="permissions[]"]');

        // Loop through the options
        Array.from(selectElement.options).forEach(option => {
            // If the option's value is in the array, mark it as selected
            if (permissions.includes(parseInt(option.value))) {
                option.selected = true;
            }
        });
    }
    
</script>

@if(session('success'))
    <script>
        toast("{{ session('success') }}");
    </script>
@endif

@endpush
</x-app-layout>
