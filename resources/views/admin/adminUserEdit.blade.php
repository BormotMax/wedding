@extends('admin.adminLayout')
@section('content')
<h1>Edit user</h1>
<div class="card">
    <div class="card-body">
    <form method="POST" action="{{url('admin/users/' . $user->id)}}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>name</label>
                <input class="form-control" type="text" name="name" required value="{{$user->name}}">
            </div>
            <div class="form-group">
                <label>role</label>
                <select class="form-control" name="role_id" required>
                    <option value="0">select role</option>
                    @foreach($roles as $role)
                        @if($role->id == $user->role_id)
                            <option value="{{$role->id}}" selected="selected">{{$role->role_name}}</option>
                        @else
                            <option value="{{$role->id}}">{{$role->role_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Update</button>
        </form>
    </div>
</div>
@endsection