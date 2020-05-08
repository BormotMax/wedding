@extends('layout')
@section('title', 'login page')
@section('content')
    <div class="card">
        <div class="card-heder">
            <h1 class="text-center">Login</h1>
        </div>
        <div class="card-body">
            <form method="POST">
                @csrf
                <div class="form-group">
                    <label>key</label>
                    <input class="form-control" type="text" name="key">
                </div>
                <button type="submit" class="btn btn-primary">sign in</button>
            </form>
        </div>
    </div>
@endsection