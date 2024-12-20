<body>
<h1>Se han detectado error en los datos de los clientes</h1>
@foreach($correos as $correo)
    <div>Cliente : {{$correo["idCliente"]}}</div>
    <div>Correo : {{$correo["email"]}}</div>
{{--    <div><a href="https://apps3.pompeyo.cl/admin/m-a/m-a-clientes/{{$correo["idCliente"]}}/edit"> Revisar Cliente </a></div>--}}
    <div><a href="https://roma.pompeyo.cl/respaldo/htmlv1/Lead.html?{{$correo["idLead"]}}"> Revisar Lead Roma</a></div>
    <div><a href="https://apps3.pompeyo.cl/admin/m-k/m-k-leads/{{$correo["idLead"]}}/edit"> Revisar Lead (apps3)</a></div>
    <hr>
@endforeach
</body>
