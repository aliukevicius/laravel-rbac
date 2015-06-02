<?php
    $statusMessage = Session::get('statusMessage', null);
?>

@if ($statusMessage != null)
    <div class="alert alert-{{ $statusMessage['type'] }}" role="alert">{{ $statusMessage['message'] }}</div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif