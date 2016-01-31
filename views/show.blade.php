@extends('backend.layouts.modal')
@section('content')
<table class="table table-hover table-striped table-bordered table-detail">
    <tr>
        <td>ID</td>
        <td><strong>{{ $user->id}}</strong></td>
    </tr>
    <tr>
        <td>{{ trans('user::user.username') }}</td>
        <td><strong>{{ $user->username }}</strong></td>
    </tr>
    <tr>
        <td>{{ trans('user::user.name') }}</td>
        <td><strong>{{ $user->name }}</strong></td>
    </tr>
    <tr>
        <td>{{ trans('user::user.role') }}</td>
        <td>{!! $user->present()->role !!}</td>
    </tr>
    <tr>
        <td>{{ trans('user::user.email') }}</td>
        <td><strong>{{ $user->email }}</strong></td>
    </tr>
    <tr>
        <td>{{ trans('common.created_at') }}</td>
        <td>{!! $user->present()->createdAt !!}</td>
    </tr>
    <tr>
        <td>{{ trans('common.updated_at') }}</td>
        <td>{!! $user->present()->updatedAt !!}</td>
    </tr>
</table>
@stop