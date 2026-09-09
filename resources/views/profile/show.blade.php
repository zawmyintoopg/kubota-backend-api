@extends('layouts.master')

@section('title', 'Profile')

@section('content')
<h4>Profile</h4>
<p>Name: {{ $user->name }}</p>
<p>Email: {{ $user->email }}</p>
@endsection
