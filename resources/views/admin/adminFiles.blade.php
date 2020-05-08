@extends('admin.adminLayout')
@section('content')
<h1>Files management</h1>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{url('admin/files')}}" enctype="multipart/form-data">
            <input type="file" class="form-control">
            <button class="btn btn-success" type="submit">send</button>
        </form>
    </div>
</div>
@endsection