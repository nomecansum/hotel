<table>
    <tr>
        <th>ID</th>
        <th style="width: 10px">COD_PUESTO</th>
        <th style="width: 40px">DES_PUESTO</th>
        <th style="width: 100px">QR</th>
    </tr>
    @foreach($puestos as $puesto)
        <tr>
            <td>{{ $puesto->id_puesto }}</td>
            <td>{{ $puesto->cod_puesto }}</td>
            <td>{{ $puesto->des_puesto }}</td>
            <td>{{ config('app.url_base_scan').$puesto->token }}</td>
        </tr>
    @endforeach
</table>