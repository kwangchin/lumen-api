@if(app()->environment('local', 'dev'))
    <script src="{{ url('bower_components/moment/moment.js') }}"></script>
@else
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
@endif
    <script src="{{ url('js/app.js') }}"></script>
    <script type="text/babel" src="{{ url('js/react-app.js') }}"></script>
</body>
</html>
