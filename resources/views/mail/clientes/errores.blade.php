<body>
<h1>Se han detectado error en los datos de los clientes</h1>
@foreach($correos as $correo)
    <div>Cliente : {{$correo["idCliente"]}}</div>
    <div>Correo : {{$correo["email"]}}</div>
    <div><a href="https://apps3.pompeyo.cl/admin/m-a/m-a-clientes/{{$correo["idCliente"]}}/edit"> Revisar </a></div>
    <hr>
@endforeach
</body>
