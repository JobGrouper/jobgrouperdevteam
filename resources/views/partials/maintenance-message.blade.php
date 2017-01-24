@if($maintenanceWarning)
    <header>
        <p class="text-center text-danger">JobGrouper will be undergoing scheduled maintenance on {{$maintenanceWarning->date}} at {{$maintenanceWarning->time}} PST for about {{$maintenanceWarning->duration}} hour{{$maintenanceWarning->duration > 1 ? 's':''}} </p>
    </header>
@endif

