@extends('backend.layouts.basic')
@section('heading', trans('user::account.reset_password'))
@section('content')
    {!! Form::open(['class' => 'm-t']) !!}
    {!! Form::hidden('token', $token) !!}
    <div class="form-group{{ $errors->has('email') ? ' has-error':'' }}">
        {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('user::account.email_reset_password'), 'required' => '']) !!}
    </div>
    <div class="form-group{{ $errors->has('password') ? ' has-error':'' }}">
        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('user::account.password_new'), 'required' => '']) !!}
    </div>
    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error':'' }}">
        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('user::account.password_confirmation'), 'required' => '']) !!}
    </div>
    <button type="submit" class="btn btn-primary block full-width m-b">{{ trans('common.save') }}</button>
    {!! Form::close() !!}
@endsection
