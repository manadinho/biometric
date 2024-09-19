<x-app-layout>
<div class="container">
    <div class="den-page-header">
        <div class="den-page-title">
            <h2>USERS</h2>
            <button class="den-btn den-create-btn" onclick="createUserForm()">+</button>
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
        <table width="100" id="den-users-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="den-users-table-body">
                @forelse($users as $user)
                    <tr id="{{ $user->id }}-row">
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                {{ $role->name }}
                            @empty
                                No Role
                            @endforelse
                        </td>
                        <td>
                            <button class="den-close-button" onclick="removeUser('{{ $user->id }}')">X</button>
                            <button class="den-edit-button" onclick="editUser('{{ $user }}')">EDIT</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No Users Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL SECTION -->
<div class="den-modal" style="visibility: hidden;">
    <div class="den-modal-content">
        <h2 class="den-modal-title">Add User</h2>

        <form id="den-user-form" method="post" action="{{ route('users.store') }}">
            <input type="hidden" id="user_id" name="user_id">
            <div class="den-form-group">
                <label for="name">Name</label>
                <input id="name" class="den-input" type="name" name="name" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group">
                <label for="email">Email</label>
                <input id="email" class="den-input" type="email" name="email" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group">
                <label for="name">Password</label>
                <input id="password" class="den-input" type="password" name="password" value="" >
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group">
                <label for="role">Select a Role</label>
                <select name="roles[]" class="den-select" id="roles">
                    <option value="">Select Role</option>
                    @forelse($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @empty
                        <option value="">No Roles Found</option>
                    @endforelse
                </select>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-button-group">
                <button class="den-btn" id="den-user-form-btn">Create</button>
            </div>
        </form>

        <button class="den-close-button" onclick="closeModal()">X</button>
    </div>
</div>

@push('scripts')

<script>
    function createUserForm() {
        resetForm('#den-user-form');
        toggleModal();
    }

    function closeModal() {
        document.querySelector('#user_id').value = '';
        toggleModal();
    }

    function removeUser(id) {
        confirmBefore('Are you sure you want to delete this user?').then(() => {
            fetch('/users/' + id, {
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

    function editUser(user) {
        user = JSON.parse(user);
        resetForm('#den-user-form');
        toggleModal();
        document.querySelector('#user_id').value = user.id;
        document.querySelector('#name').value = user.name;
        document.querySelector('#email').value = user.email;
        const roles = user.roles.map(role => role.id);

        var selectElement = document.querySelector('select[name="roles[]"]');

        // Loop through the options
        Array.from(selectElement.options).forEach(option => {
            // If the option's value is in the array, mark it as selected
            if (roles.includes(parseInt(option.value))) {
                option.selected = true;
            }
        });
    }
</script>

@endpush
</x-app-layout>
