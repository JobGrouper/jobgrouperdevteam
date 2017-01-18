@if($maintenanceWarning)
    <header>
        <p class="text-center text-danger">{{$maintenanceWarning->text}} <b>From:</b>  {{$maintenanceWarning->date_from}} <b>To:</b> {{$maintenanceWarning->date_to}}</p>
    </header>
@endif

