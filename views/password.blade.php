@extends('backend.layouts.basic')
@section('heading', trans('user::account.reset_password'))
@section('content')
    {!! Form::open(['class' => 'm-t']) !!}
    <div class="form-group{{ $errors->has('email') ? ' has-error':'' }}">
        {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('user::account.email_reset_password'), 'required' => '']) !!}
    </div>
    <button type="submit"
            class="btn btn-primary block full-width m-b">{{ trans('user::account.send_password_reset_link') }}</button>
    {!! Form::close() !!}
@endsection