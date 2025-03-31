<body>
<h1>Se han detectado un error en uno de los procesos de Apps3</h1>

Flujo : {{$monitor->flujo->Nombre}}
Accion : {{$monitor->Accion}}
Error : {{$monitor->Mensaje}}
Fecha : {{$monitor->created_at}}
Fecha de ejecucion : {{$monitor->FechaInicio}}
Fecha de termino : {{$monitor->FechaTermino}}

<p>Por favor, revisar el log de la aplicacion para mas detalles.</p>
</body>
