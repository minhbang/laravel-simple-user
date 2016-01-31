@extends('backend.layouts.basic')

@section('heading', trans('user::account.login_title'))

@section('content')
    {!! Form::open(['class' => 'm-t']) !!}
    <div class="form-group{{ $errors->has('username') ? ' has-error':'' }}">
        {!! Form::text('username', old('username'), ['class' => 'form-control', 'placeholder' => trans('user::account.username'), 'required' => '']) !!}
        @if($errors->has('username'))
            <p class="help-block">{{ $errors->first('username') }}</p>
        @endif
    </div>
    <div class="form-group{{ $errors->has('password') ? ' has-error':'' }}">
        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('user::account.password'), 'required' => '']) !!}
        @if($errors->has('password'))
            <p class="help-block">{{ $errors->first('password') }}</p>
        @endif
    </div>
    <div class="form-group">
        <div class="checkbox checkbox-warning">
            {!! Form::checkbox('remember', '1', old('remember'),  ['id' => 'remember']) !!}
            <label for="remember">{{ trans('user::account.remember') }}</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary block full-width m-b">{{ trans('user::account.login') }}</button>
    <a href="{{route('password.email')}}">
        <small>{{trans('user::account.forgot_password')}}</small>
    </a>
    {!! Form::close() !!}
@stop
