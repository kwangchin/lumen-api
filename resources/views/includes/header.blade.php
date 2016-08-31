<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lumen API</title>

    <link rel="stylesheet" href="{{ url('bower_components/normalize.css/normalize.css') }}">
    <link rel="stylesheet" href="{{ url('css/style.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}">

@if(app()->environment('local', 'dev'))
    <script src="{{ url('bower_components/react/react.js') }}"></script>
    <script src="{{ url('bower_components/react/react-dom.js') }}"></script>
    <script src="{{ url('bower_components/babel/browser.js') }}"></script>
    <script src="{{ url('bower_components/jquery/dist/jquery.js') }}"></script>
    <script src="{{ url('bower_components/remarkable/dist/remarkable.js') }}"></script>
@else
    <script src="{{ url('bower_components/react/react.min.js') }}"></script>
    <script src="{{ url('bower_components/react/react-dom.min.js') }}"></script>
    <script src="{{ url('bower_components/babel/browser.min.js') }}"></script>
    <script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ url('bower_components/remarkable/dist/remarkable.min.js') }}"></script>
@endif
</head>
<body>
