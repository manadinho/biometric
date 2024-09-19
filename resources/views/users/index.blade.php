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
    
    <div>
        <table width="100" id="den-users-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="den-users-table-body">
                @forelse($users as $user)
                    <tr id="{{ $user->id }}-row">
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
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

        <form id="den-user-form">
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
                <input id="password" class="den-input" type="password" name="password" value="" required autofocus>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-form-group">
                <label for="role">Select a Role</label>
                <select name="role" class="den-select" id="roles">
                </select>
                <!-- <div class="den-error">Error messages here</div> -->
            </div>

            <div class="den-button-group">
                <button class="den-btn" id="den-user-form-btn">Create</button>
            </div>
        </form>

        <button class="den-close-button" onclick="showHideModal()">X</button>
    </div>
</div>
</x-app-layout>

@push('scripts')

<script>
    alert('Hello from users index');
</script>

@endpush
