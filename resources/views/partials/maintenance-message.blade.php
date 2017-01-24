@if($maintenanceWarning)
    <header>
        <p class="text-center text-danger">Maintenance mod will be {{$maintenanceWarning->date}} at {{$maintenanceWarning->time}} during {{$maintenanceWarning->duration}} hour{{$maintenanceWarning->duration > 1 ? 's':''}} </p>
    </header>
@endif

