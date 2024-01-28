<table>
    <tr>
        <th>ID</th>
        <th style="width: 10px">Nombre</th>
        <th style="width: 10px">Empresa</th>
        <th style="width: 10px">e-mail</th>
        <th style="width: 100px">QR</th>
    </tr>
    @foreach($datos as $dato)
        <tr>
            <td>{{ $dato->id_contacto }}</td>
            <td>{{ $dato->nombre }}</td>
            <td>{{ $dato->empresa }}</td>
            <td>{{ $dato->email }}</td>
            <td>{{ $dato->token }}</td>
        </tr>
    @endforeach
</table>