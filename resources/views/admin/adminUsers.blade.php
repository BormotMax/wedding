@extends('admin.adminLayout')
@section('content')
<h1>Users Management</h1>
<table class="table admin-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Link</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>
                {{$user->name}}
            </td>
            <td>
                {{$user->key}}
            </td>
            <td>
                {{$user->role->role_name}}
            </td>
            <td class="action-group">
                <form method="POST" action="{{url('admin/users/' . $user->id)}}">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-sx btn-danger">delete</button>
                </form>
                <a class="btn btn-sx btn-warning" href="{{url('admin/users/' . $user->id . '/edit')}}">
                    edit
                </a>
            </td>
        </tr>
    @endforeach
    </tdody>
</table>
<div class="card">
        <div class="card-heder">
            <h1 class="text-center">Create new user</h1>
        </div>
        <div class="card-body">
        <form method="POST" action="{{url('admin/users')}}">    
                @csrf
                <div class="form-group">
                    <label>name</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>role</label>
                    <select class="form-control" name="role_id" required>
                        <option value="0">select role</option>
                        @foreach($roles as $role)
                            <option value="{{$role->id}}">{{$role->role_name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
@endsection